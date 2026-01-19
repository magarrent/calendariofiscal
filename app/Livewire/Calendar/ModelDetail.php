<?php

namespace App\Livewire\Calendar;

use App\Models\TaxModel;
use Livewire\Attributes\On;
use Livewire\Component;

class ModelDetail extends Component
{
    public ?int $modelId = null;

    public bool $showDetails = false;

    #[On('load-model')]
    public function loadModel(int $modelId): void
    {
        $this->modelId = $modelId;
        $this->showDetails = false;
    }

    public function getTaxModel(): ?TaxModel
    {
        return TaxModel::with('deadlines')->find($this->modelId);
    }

    public function toggleDetails(): void
    {
        $this->showDetails = ! $this->showDetails;
    }

    public function getCategoryColor(string $category): string
    {
        return match ($category) {
            'iva' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'irpf' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'retenciones' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'sociedades' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    public function getFrequencyLabel(string $frequency): string
    {
        return match ($frequency) {
            'monthly' => 'Mensual',
            'quarterly' => 'Trimestral',
            'annual' => 'Anual',
            'one-time' => 'Único',
            default => $frequency,
        };
    }

    public function render()
    {
        return view('livewire.calendar.model-detail', [
            'model' => $this->getTaxModel(),
        ]);
    }
}
