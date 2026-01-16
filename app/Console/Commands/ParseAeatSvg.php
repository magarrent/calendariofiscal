<?php

namespace App\Console\Commands;

use App\Models\Deadline;
use App\Models\TaxModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ParseAeatSvg extends Command
{
    protected $signature = 'aeat:parse-svg {file? : Path to the SVG file} {--year=2026 : Year for the tax models}';

    protected $description = 'Parse AEAT SVG file and import tax models and deadlines into the database';

    public function handle(): int
    {
        $filePath = $this->argument('file') ?? base_path('modelos_aeat_2026_super_detallado.svg');
        $year = (int) $this->option('year');

        if (! file_exists($filePath)) {
            $this->error("SVG file not found at: {$filePath}");
            $this->info('Please provide the correct path to the SVG file.');

            return self::FAILURE;
        }

        $this->info("Parsing AEAT SVG file: {$filePath}");
        $this->info("Year: {$year}");

        try {
            $svgContent = file_get_contents($filePath);
            $xml = simplexml_load_string($svgContent);

            if ($xml === false) {
                $this->error('Failed to parse SVG file as XML');

                return self::FAILURE;
            }

            $this->info('SVG file loaded successfully');

            $taxModels = $this->extractTaxModelsFromSvg($xml, $year);

            if (empty($taxModels)) {
                $this->warn('No tax models found in the SVG file');
                $this->info('The SVG parser may need customization based on the specific file structure');

                return self::FAILURE;
            }

            $this->info('Found '.count($taxModels).' tax models');

            DB::beginTransaction();

            try {
                $importedCount = 0;
                $skippedCount = 0;

                foreach ($taxModels as $modelData) {
                    $existing = TaxModel::where('model_number', $modelData['model_number'])
                        ->where('year', $year)
                        ->first();

                    if ($existing) {
                        $this->warn("Skipping existing model: {$modelData['model_number']}");
                        $skippedCount++;

                        continue;
                    }

                    $taxModel = TaxModel::create([
                        'model_number' => $modelData['model_number'],
                        'name' => $modelData['name'],
                        'description' => $modelData['description'] ?? null,
                        'instructions' => $modelData['instructions'] ?? null,
                        'penalties' => $modelData['penalties'] ?? null,
                        'frequency' => $modelData['frequency'],
                        'applicable_to' => $modelData['applicable_to'],
                        'aeat_url' => $modelData['aeat_url'] ?? 'https://sede.agenciatributaria.gob.es',
                        'category' => $modelData['category'],
                        'year' => $year,
                    ]);

                    if (! empty($modelData['deadlines'])) {
                        foreach ($modelData['deadlines'] as $deadlineData) {
                            Deadline::create([
                                'tax_model_id' => $taxModel->id,
                                'deadline_date' => $deadlineData['date'],
                                'deadline_time' => $deadlineData['time'] ?? null,
                                'year' => $year,
                                'notes' => $deadlineData['notes'] ?? null,
                            ]);
                        }
                    }

                    $importedCount++;
                    $this->line("✓ Imported: {$modelData['model_number']} - {$modelData['name']}");
                }

                DB::commit();

                $this->newLine();
                $this->info('Import completed successfully!');
                $this->info("Imported: {$importedCount} models");
                $this->info("Skipped: {$skippedCount} models (already exist)");

                return self::SUCCESS;
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            $this->error('Error parsing SVG file: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    protected function extractTaxModelsFromSvg(\SimpleXMLElement $xml, int $year): array
    {
        $taxModels = [];
        $xml->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');

        $textElements = $xml->xpath('//svg:text');

        $currentModel = null;
        $modelPattern = '/^(Modelo\s+)?(\d{3}[A-Z]?)/i';
        $datePattern = '/(\d{1,2})[\/\-](\d{1,2})(?:[\/\-](\d{2,4}))?/';

        foreach ($textElements as $text) {
            $content = trim((string) $text);

            if (empty($content)) {
                continue;
            }

            if (preg_match($modelPattern, $content, $matches)) {
                $modelNumber = $matches[2];

                $category = $this->inferCategory($modelNumber, $content);
                $frequency = $this->inferFrequency($content);

                $currentModel = [
                    'model_number' => $modelNumber,
                    'name' => $content,
                    'description' => null,
                    'instructions' => null,
                    'penalties' => null,
                    'frequency' => $frequency,
                    'applicable_to' => ['autonomo', 'pyme', 'large_corp'],
                    'aeat_url' => 'https://sede.agenciatributaria.gob.es',
                    'category' => $category,
                    'deadlines' => [],
                ];

                $taxModels[] = $currentModel;
            } elseif (preg_match($datePattern, $content, $dateMatches)) {
                if (! empty($taxModels)) {
                    $day = str_pad($dateMatches[1], 2, '0', STR_PAD_LEFT);
                    $month = str_pad($dateMatches[2], 2, '0', STR_PAD_LEFT);
                    $dateYear = $dateMatches[3] ?? $year;

                    if (strlen($dateYear) == 2) {
                        $dateYear = '20'.$dateYear;
                    }

                    $deadlineDate = "{$dateYear}-{$month}-{$day}";

                    $lastIndex = count($taxModels) - 1;
                    $taxModels[$lastIndex]['deadlines'][] = [
                        'date' => $deadlineDate,
                        'time' => null,
                        'notes' => null,
                    ];
                }
            }
        }

        return $taxModels;
    }

    protected function inferCategory(string $modelNumber, string $content): string
    {
        $content = strtolower($content);

        if (str_contains($content, 'iva') || in_array($modelNumber, ['303', '390', '349'])) {
            return 'iva';
        }

        if (str_contains($content, 'irpf') || in_array($modelNumber, ['100', '130', '131'])) {
            return 'irpf';
        }

        if (str_contains($content, 'retenc') || in_array($modelNumber, ['111', '115', '123', '124'])) {
            return 'retenciones';
        }

        if (str_contains($content, 'sociedad') || in_array($modelNumber, ['200', '220', '202'])) {
            return 'sociedades';
        }

        return 'otros';
    }

    protected function inferFrequency(string $content): string
    {
        $content = strtolower($content);

        if (str_contains($content, 'mensual') || str_contains($content, 'monthly')) {
            return 'monthly';
        }

        if (str_contains($content, 'trimestral') || str_contains($content, 'quarterly')) {
            return 'quarterly';
        }

        if (str_contains($content, 'anual') || str_contains($content, 'annual')) {
            return 'annual';
        }

        return 'quarterly';
    }
}
