<?php

namespace Database\Seeders;

use App\Models\Deadline;
use App\Models\TaxModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CsvTaxModelSeeder extends Seeder
{
    public function run(): void
    {
        // Path to the new comprehensive CSV
        $csvPath = storage_path('app/private/mega_catalogo_modelos_aeat_2026_solo_modelos_con_ambito_empresa.csv');

        if (! file_exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");

            return;
        }

        $this->command->info('Reading CSV file...');

        // Read CSV and group by modelo
        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv); // Remove header row

        // Remove BOM from first header if present
        if (isset($headers[0])) {
            $headers[0] = ltrim($headers[0], "\xEF\xBB\xBF");
        }

        // Group deadlines by modelo number
        $groupedData = [];

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);

            $modelNumber = $data['modelo'];

            if (! isset($groupedData[$modelNumber])) {
                $groupedData[$modelNumber] = [
                    'model_data' => $data,
                    'deadlines' => [],
                ];
            }

            $groupedData[$modelNumber]['deadlines'][] = $data;
        }

        $this->command->info('Processing '.count($groupedData).' tax models...');

        DB::transaction(function () use ($groupedData) {
            foreach ($groupedData as $modelNumber => $group) {
                $modelData = $group['model_data'];

                // Parse applicable_to from ambito field and conditions
                $applicableTo = $this->parseApplicableTo(
                    $modelData['ambito'] ?? '',
                    $modelData['condicion'] ?? ''
                );

                // Infer frequency from the deadlines
                $frequency = $this->inferFrequency($group['deadlines']);

                // Extract year from deadline date
                $year = 2026; // Default year
                if (! empty($modelData['fecha_fin'])) {
                    $year = (int) substr($modelData['fecha_fin'], 0, 4);
                }

                // Map category to simplified format
                $category = $this->mapCategory($modelData['categoria'] ?? '');

                // Create or update TaxModel
                $taxModel = TaxModel::updateOrCreate(
                    ['model_number' => $modelNumber],
                    [
                        'name' => $modelData['titulo'] ?? 'Modelo '.$modelNumber,
                        'description' => $modelData['categoria'] ?? null,
                        'group_description' => $modelData['grupo_desc'] ?? null,
                        'category' => $category,
                        'frequency' => $frequency,
                        'applicable_to' => $applicableTo,
                        'year' => $year,
                        'source_document' => $modelData['fuente'] ?? null,
                        'aeat_url' => 'https://sede.agenciatributaria.gob.es',
                    ]
                );

                // Create deadlines (use updateOrCreate to handle duplicate UIDs)
                foreach ($group['deadlines'] as $deadlineData) {
                    $uid = $deadlineData['uid'] ?? null;

                    // Use updateOrCreate if UID exists, otherwise just create
                    if ($uid) {
                        Deadline::updateOrCreate(
                            ['uid' => $uid],
                            [
                                'tax_model_id' => $taxModel->id,
                                'deadline_date' => $this->parseDate($deadlineData['fecha_fin']),
                                'deadline_time' => null, // Not in CSV
                                'period' => $deadlineData['periodo'] ?? null,
                                'period_start' => $this->parseDate($deadlineData['fecha_inicio']),
                                'period_end' => $this->parseDate($deadlineData['fecha_fin']),
                                'deadline_label' => $deadlineData['plazo_label'] ?? null,
                                'check_day_1' => $this->parseDateTime($deadlineData['check_dia_1']),
                                'check_day_10' => $this->parseDateTime($deadlineData['check_dia_10']),
                                'rule_start_date' => $this->parseDate($deadlineData['fecha_inicio_regla']),
                                'is_variable' => $this->parseBoolean($deadlineData['plazo_variable']),
                                'page_number' => $this->parseInt($deadlineData['pagina_pdf']),
                                'details' => $deadlineData['detalle'] ?? null,
                                'deadline_scope' => $deadlineData['ambito_plazo'] ?? null,
                                'conditions' => $deadlineData['condicion'] ?? null,
                                'year' => $year,
                            ]
                        );
                    } else {
                        Deadline::create([
                            'tax_model_id' => $taxModel->id,
                            'uid' => null,
                            'deadline_date' => $this->parseDate($deadlineData['fecha_fin']),
                            'deadline_time' => null,
                            'period' => $deadlineData['periodo'] ?? null,
                            'period_start' => $this->parseDate($deadlineData['fecha_inicio']),
                            'period_end' => $this->parseDate($deadlineData['fecha_fin']),
                            'deadline_label' => $deadlineData['plazo_label'] ?? null,
                            'check_day_1' => $this->parseDateTime($deadlineData['check_dia_1']),
                            'check_day_10' => $this->parseDateTime($deadlineData['check_dia_10']),
                            'rule_start_date' => $this->parseDate($deadlineData['fecha_inicio_regla']),
                            'is_variable' => $this->parseBoolean($deadlineData['plazo_variable']),
                            'page_number' => $this->parseInt($deadlineData['pagina_pdf']),
                            'details' => $deadlineData['detalle'] ?? null,
                            'deadline_scope' => $deadlineData['ambito_plazo'] ?? null,
                            'conditions' => $deadlineData['condicion'] ?? null,
                            'year' => $year,
                        ]);
                    }
                }

                $this->command->info("Created/Updated: Modelo {$modelNumber} with ".count($group['deadlines']).' deadlines');
            }
        });

        $this->command->info('Tax models seeded successfully!');
    }

    private function parseApplicableTo(string $ambito, string $condicion = ''): array
    {
        $mapping = [
            'Autónomo' => 'autonomo',
            'Sociedad' => 'pyme',
            'Entidad' => 'pyme',
            'Gran Empresa' => 'large_corp',
            'Empresa sectorial' => 'large_corp',
            'Entidad financiera' => 'large_corp',
            'Persona física' => 'autonomo',
            'Otros' => 'autonomo',
        ];

        $parts = array_map('trim', explode(';', $ambito));
        $result = [];

        foreach ($parts as $part) {
            if (isset($mapping[$part])) {
                $result[] = $mapping[$part];
            }
        }

        // Filter based on conditions that clearly exclude certain types
        $result = $this->filterByConditions($result, $condicion);

        // If no match and ambito is empty, don't default to all - return empty
        if (empty($result) && empty($ambito)) {
            return [];
        }

        // If no match but ambito had content, default to all
        return array_unique(array_filter($result)) ?: ['autonomo', 'pyme', 'large_corp'];
    }

    private function filterByConditions(array $applicableTo, string $condicion): array
    {
        if (empty($condicion)) {
            return $applicableTo;
        }

        $condicionLower = mb_strtolower($condicion);

        // Exclusions for autonomo
        $autonomoExclusions = [
            'solo grupos de entidades',
            'solo entidades financieras',
            'entidades financieras/aseguradoras',
            'solo empresas del sector',
            'solo grandes empresas',
        ];

        // Exclusions for pyme
        $pymeExclusions = [
            'solo entidades financieras',
            'entidades financieras/aseguradoras',
            'solo grandes empresas',
        ];

        // Check if autonomo should be excluded
        foreach ($autonomoExclusions as $exclusion) {
            if (str_contains($condicionLower, $exclusion)) {
                $applicableTo = array_diff($applicableTo, ['autonomo']);
                break;
            }
        }

        // Check if pyme should be excluded
        foreach ($pymeExclusions as $exclusion) {
            if (str_contains($condicionLower, $exclusion)) {
                $applicableTo = array_diff($applicableTo, ['pyme']);
                break;
            }
        }

        return array_values($applicableTo);
    }

    private function inferFrequency(array $deadlines): string
    {
        $count = count($deadlines);

        if ($count >= 12) {
            return 'monthly';
        }

        if ($count >= 4) {
            return 'quarterly';
        }

        if ($count <= 1) {
            return 'annual';
        }

        // Check period descriptions for hints
        foreach ($deadlines as $deadline) {
            $period = strtolower($deadline['periodo'] ?? '');

            if (str_contains($period, 'trimestre')) {
                return 'quarterly';
            }

            if (str_contains($period, 'año') || str_contains($period, 'ejercicio')) {
                return 'annual';
            }

            if (preg_match('/(enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre)/i', $period)) {
                return 'monthly';
            }
        }

        return 'one-time';
    }

    private function mapCategory(string $categoria): string
    {
        $mapping = [
            'IVA' => 'iva',
            'Renta y Sociedades' => 'retenciones',
            'Pagos fraccionados Renta' => 'irpf',
            'IRPF' => 'irpf',
            'Retenciones' => 'retenciones',
            'Impuesto sobre Sociedades' => 'sociedades',
            'Informativa' => 'otros',
            'Censal' => 'otros',
        ];

        foreach ($mapping as $pattern => $category) {
            if (str_contains($categoria, $pattern)) {
                return $category;
            }
        }

        return 'otros';
    }

    private function parseDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return date('Y-m-d', strtotime($date));
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseDateTime(?string $datetime): ?string
    {
        if (empty($datetime)) {
            return null;
        }

        try {
            return date('Y-m-d H:i:s', strtotime($datetime));
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseBoolean(?string $value): bool
    {
        return strtolower($value ?? '') === 'true';
    }

    private function parseInt(?string $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }
}
