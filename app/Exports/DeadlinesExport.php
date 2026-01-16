<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DeadlinesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        public Collection $deadlines
    ) {}

    public function collection(): Collection
    {
        return $this->deadlines;
    }

    public function headings(): array
    {
        return [
            'Número de Modelo',
            'Nombre del Modelo',
            'Categoría',
            'Periodicidad',
            'Fecha Límite',
            'Hora Límite',
            'Año',
            'Aplicable a',
            'Notas',
            'URL AEAT',
        ];
    }

    public function map($deadline): array
    {
        $taxModel = $deadline->taxModel;

        return [
            $taxModel?->model_number ?? '',
            $taxModel?->name ?? '',
            $taxModel?->category ?? '',
            $taxModel?->frequency ?? '',
            $deadline->deadline_date?->format('Y-m-d') ?? '',
            $deadline->deadline_time?->format('H:i') ?? '',
            $deadline->year,
            is_array($taxModel?->applicable_to) ? implode(', ', $taxModel->applicable_to) : '',
            $deadline->notes ?? '',
            $taxModel?->aeat_url ?? '',
        ];
    }
}
