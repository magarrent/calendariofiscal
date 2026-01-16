<?php

namespace Database\Seeders;

use App\Models\Deadline;
use App\Models\TaxModel;
use Illuminate\Database\Seeder;

class TaxModelSeeder extends Seeder
{
    public function run(): void
    {
        $taxModels = [
            [
                'model_number' => '303',
                'name' => 'Modelo 303 - IVA Autoliquidación',
                'description' => 'Autoliquidación del Impuesto sobre el Valor Añadido',
                'instructions' => 'Declaración mensual o trimestral del IVA',
                'penalties' => 'Recargo del 5% al 20% según el retraso',
                'frequency' => 'monthly',
                'applicable_to' => ['autonomo', 'pyme', 'large_corp'],
                'category' => 'iva',
                'aeat_url' => 'https://sede.agenciatributaria.gob.es/Sede/iva.html',
                'year' => 2026,
                'deadlines' => [
                    ['deadline_date' => '2026-01-20'],
                    ['deadline_date' => '2026-02-20'],
                    ['deadline_date' => '2026-03-20'],
                    ['deadline_date' => '2026-04-20'],
                    ['deadline_date' => '2026-05-20'],
                    ['deadline_date' => '2026-06-20'],
                    ['deadline_date' => '2026-07-20'],
                    ['deadline_date' => '2026-08-20'],
                    ['deadline_date' => '2026-09-20'],
                    ['deadline_date' => '2026-10-20'],
                    ['deadline_date' => '2026-11-20'],
                    ['deadline_date' => '2026-12-21'],
                ],
            ],
            [
                'model_number' => '130',
                'name' => 'Modelo 130 - IRPF Pago Fraccionado',
                'description' => 'Pago fraccionado del IRPF para actividades económicas',
                'instructions' => 'Declaración trimestral del pago fraccionado',
                'penalties' => 'Recargo del 5% al 20% según el retraso',
                'frequency' => 'quarterly',
                'applicable_to' => ['autonomo'],
                'category' => 'irpf',
                'aeat_url' => 'https://sede.agenciatributaria.gob.es/Sede/irpf.html',
                'year' => 2026,
                'deadlines' => [
                    ['deadline_date' => '2026-04-20'],
                    ['deadline_date' => '2026-07-20'],
                    ['deadline_date' => '2026-10-20'],
                    ['deadline_date' => '2027-01-20'],
                ],
            ],
            [
                'model_number' => '111',
                'name' => 'Modelo 111 - Retenciones e ingresos a cuenta',
                'description' => 'Retenciones e ingresos a cuenta del trabajo personal',
                'instructions' => 'Declaración trimestral de retenciones',
                'penalties' => 'Recargo del 5% al 20% según el retraso',
                'frequency' => 'quarterly',
                'applicable_to' => ['autonomo', 'pyme', 'large_corp'],
                'category' => 'retenciones',
                'aeat_url' => 'https://sede.agenciatributaria.gob.es',
                'year' => 2026,
                'deadlines' => [
                    ['deadline_date' => '2026-04-20'],
                    ['deadline_date' => '2026-07-20'],
                    ['deadline_date' => '2026-10-20'],
                    ['deadline_date' => '2027-01-20'],
                ],
            ],
            [
                'model_number' => '390',
                'name' => 'Modelo 390 - IVA Resumen Anual',
                'description' => 'Declaración resumen anual del IVA',
                'instructions' => 'Resumen anual de las operaciones con IVA',
                'penalties' => 'Multa fija de 200€ a 20.000€',
                'frequency' => 'annual',
                'applicable_to' => ['autonomo', 'pyme', 'large_corp'],
                'category' => 'iva',
                'aeat_url' => 'https://sede.agenciatributaria.gob.es/Sede/iva.html',
                'year' => 2026,
                'deadlines' => [
                    ['deadline_date' => '2027-01-30'],
                ],
            ],
            [
                'model_number' => '100',
                'name' => 'Modelo 100 - IRPF Declaración Anual',
                'description' => 'Declaración anual del Impuesto sobre la Renta de las Personas Físicas',
                'instructions' => 'Declaración de la renta anual',
                'penalties' => 'Recargo del 5% al 20% según el retraso, más intereses',
                'frequency' => 'annual',
                'applicable_to' => ['autonomo', 'pyme'],
                'category' => 'irpf',
                'aeat_url' => 'https://sede.agenciatributaria.gob.es/Sede/irpf.html',
                'year' => 2026,
                'deadlines' => [
                    ['deadline_date' => '2026-06-30'],
                ],
            ],
            [
                'model_number' => '349',
                'name' => 'Modelo 349 - Operaciones Intracomunitarias',
                'description' => 'Declaración recapitulativa de operaciones intracomunitarias',
                'instructions' => 'Declaración mensual o trimestral según volumen',
                'penalties' => 'Multa fija de 300€ a 6.000€',
                'frequency' => 'monthly',
                'applicable_to' => ['autonomo', 'pyme', 'large_corp'],
                'category' => 'iva',
                'aeat_url' => 'https://sede.agenciatributaria.gob.es',
                'year' => 2026,
                'deadlines' => [
                    ['deadline_date' => '2026-01-20'],
                    ['deadline_date' => '2026-02-20'],
                    ['deadline_date' => '2026-03-20'],
                    ['deadline_date' => '2026-04-20'],
                    ['deadline_date' => '2026-05-20'],
                    ['deadline_date' => '2026-06-20'],
                    ['deadline_date' => '2026-07-20'],
                    ['deadline_date' => '2026-08-20'],
                    ['deadline_date' => '2026-09-20'],
                    ['deadline_date' => '2026-10-20'],
                    ['deadline_date' => '2026-11-20'],
                    ['deadline_date' => '2026-12-21'],
                ],
            ],
            [
                'model_number' => '200',
                'name' => 'Modelo 200 - Impuesto sobre Sociedades',
                'description' => 'Declaración del Impuesto sobre Sociedades',
                'instructions' => 'Declaración anual del impuesto de sociedades',
                'penalties' => 'Recargo del 5% al 20% según el retraso',
                'frequency' => 'annual',
                'applicable_to' => ['pyme', 'large_corp'],
                'category' => 'sociedades',
                'aeat_url' => 'https://sede.agenciatributaria.gob.es',
                'year' => 2026,
                'deadlines' => [
                    ['deadline_date' => '2026-07-25'],
                ],
            ],
            [
                'model_number' => '115',
                'name' => 'Modelo 115 - Retenciones e ingresos a cuenta Arrendamientos',
                'description' => 'Retenciones e ingresos a cuenta de arrendamientos de inmuebles urbanos',
                'instructions' => 'Declaración trimestral de retenciones por alquileres',
                'penalties' => 'Recargo del 5% al 20% según el retraso',
                'frequency' => 'quarterly',
                'applicable_to' => ['autonomo', 'pyme', 'large_corp'],
                'category' => 'retenciones',
                'aeat_url' => 'https://sede.agenciatributaria.gob.es',
                'year' => 2026,
                'deadlines' => [
                    ['deadline_date' => '2026-04-20'],
                    ['deadline_date' => '2026-07-20'],
                    ['deadline_date' => '2026-10-20'],
                    ['deadline_date' => '2027-01-20'],
                ],
            ],
        ];

        foreach ($taxModels as $modelData) {
            $deadlines = $modelData['deadlines'];
            unset($modelData['deadlines']);

            $taxModel = TaxModel::create($modelData);

            foreach ($deadlines as $deadlineData) {
                Deadline::create([
                    'tax_model_id' => $taxModel->id,
                    'deadline_date' => $deadlineData['deadline_date'],
                    'year' => $taxModel->year,
                ]);
            }
        }
    }
}
