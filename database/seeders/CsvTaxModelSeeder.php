<?php

namespace Database\Seeders;

use App\Models\Deadline;
use App\Models\TaxModel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class CsvTaxModelSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = storage_path('app/private/modelos_aeat_2026_super_detallado.csv');

        if (! file_exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");

            return;
        }

        $this->command->info('Reading CSV file...');

        $file = fopen($csvPath, 'r');

        // Skip BOM if present
        $bom = fread($file, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($file);
        }

        // Read header
        $header = fgetcsv($file);

        if (! $header) {
            $this->command->error('Failed to read CSV header');

            return;
        }

        $rowCount = 0;
        $parsedCount = 0;
        $errorCount = 0;

        // Store tax models grouped by model number to avoid duplicates
        $taxModelsCache = [];

        while (($row = fgetcsv($file)) !== false) {
            $rowCount++;

            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            try {
                $data = array_combine($header, $row);

                if (! $data || empty($data['modelo']) || empty($data['nombre'])) {
                    $this->command->warn("Skipping row {$rowCount}: Missing required fields");
                    $errorCount++;

                    continue;
                }

                // Parse dates with error handling
                $periodStart = $this->parseDate($data['plazo_inicio_2026'] ?? '');
                $periodEnd = $this->parseDate($data['plazo_fin_2026'] ?? '');

                if (! $periodStart || ! $periodEnd) {
                    $this->command->warn("Skipping row {$rowCount}: Invalid dates");
                    $errorCount++;

                    continue;
                }

                // Calculate days to complete
                $daysToComplete = $periodStart->diffInDays($periodEnd);

                // Get or create tax model
                $modelNumber = trim($data['modelo']);
                $modelKey = $modelNumber.'_'.$data['categoria'].'_'.$data['periodicidad'];

                if (! isset($taxModelsCache[$modelKey])) {
                    $taxModel = TaxModel::firstOrCreate(
                        [
                            'model_number' => $modelNumber,
                            'category' => $this->parseCategory($data['categoria'] ?? ''),
                            'frequency' => $this->parseFrequency($data['periodicidad'] ?? ''),
                            'year' => 2026,
                        ],
                        [
                            'name' => trim($data['nombre']),
                            'description' => trim($data['tipo_declaracion'] ?? ''),
                            'instructions' => trim($data['obligados_tipicos'] ?? ''),
                            'applicable_to' => $this->parseApplicableTo($data['obligados_tipicos'] ?? ''),
                            'aeat_url' => $this->parseAeatUrl($data['fuente_oficial'] ?? ''),
                            'penalties' => null,
                        ]
                    );

                    $taxModelsCache[$modelKey] = $taxModel;
                } else {
                    $taxModel = $taxModelsCache[$modelKey];
                }

                // Create deadline
                Deadline::create([
                    'tax_model_id' => $taxModel->id,
                    'deadline_date' => $periodEnd,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'days_to_complete' => $daysToComplete,
                    'period_description' => trim($data['periodo_o_ejercicio'] ?? ''),
                    'year' => 2026,
                    'notes' => trim($data['notas'] ?? ''),
                ]);

                $parsedCount++;
            } catch (\Exception $e) {
                $this->command->error("Error parsing row {$rowCount}: ".$e->getMessage());
                $errorCount++;
                Log::error("CSV Parsing Error on row {$rowCount}", [
                    'error' => $e->getMessage(),
                    'row' => $row ?? null,
                ]);
            }
        }

        fclose($file);

        $this->command->info('✅ CSV parsing complete!');
        $this->command->info("Total rows processed: {$rowCount}");
        $this->command->info("Successfully parsed: {$parsedCount}");
        $this->command->info("Errors: {$errorCount}");
        $this->command->info('Tax models created: '.count($taxModelsCache));
    }

    private function parseDate(?string $date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', trim($date));
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseCategory(string $category): string
    {
        $category = strtolower(trim($category));

        // Normalize categories
        return match (true) {
            str_contains($category, 'iva') => 'iva',
            str_contains($category, 'irpf') => 'irpf',
            str_contains($category, 'retenciones') => 'retenciones',
            str_contains($category, 'informativa') => 'informativa',
            str_contains($category, 'censal') => 'censal',
            str_contains($category, 'sociedades') => 'sociedades',
            str_contains($category, 'intracomunitario') => 'iva',
            str_contains($category, 'nif') => 'informativa',
            default => $category,
        };
    }

    private function parseFrequency(string $frequency): string
    {
        $frequency = strtolower(trim($frequency));

        return match (true) {
            str_contains($frequency, 'mensual') => 'monthly',
            str_contains($frequency, 'trimestral') => 'quarterly',
            str_contains($frequency, 'anual') => 'annual',
            default => 'one-time',
        };
    }

    private function parseApplicableTo(string $obligados): array
    {
        $obligados = strtolower($obligados);
        $types = [];

        if (str_contains($obligados, 'autónomo') || str_contains($obligados, 'personas físicas')) {
            $types[] = 'autonomo';
        }

        if (str_contains($obligados, 'empresas') || str_contains($obligados, 'pyme') || str_contains($obligados, 'empresarios')) {
            $types[] = 'pyme';
        }

        if (str_contains($obligados, 'grandes empresas') || str_contains($obligados, 'entidades')) {
            $types[] = 'large_corp';
        }

        // If no specific type identified, default to all
        if (empty($types)) {
            $types = ['autonomo', 'pyme', 'large_corp'];
        }

        return array_unique($types);
    }

    private function parseAeatUrl(string $fuente): ?string
    {
        if (empty($fuente)) {
            return null;
        }

        // Extract first URL from the fuente field
        if (preg_match('/\((https?:\/\/[^\)]+)\)/', $fuente, $matches)) {
            return $matches[1];
        }

        // Default AEAT URL
        return 'https://sede.agenciatributaria.gob.es';
    }
}
