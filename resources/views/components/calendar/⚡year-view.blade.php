<?php

use Livewire\Component;
use Illuminate\Support\Collection;
use Carbon\Carbon;

new class extends Component
{
    public Collection $deadlines;
    public Carbon $currentDate;

    public function mount(Collection $deadlines, Carbon $currentDate): void
    {
        $this->deadlines = $deadlines;
        $this->currentDate = $currentDate;
    }

    public function getMonthsData(): array
    {
        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create($this->currentDate->year, $month, 1);
            $months[] = [
                'date' => $date,
                'deadlines' => $this->getDeadlinesForMonth($date),
            ];
        }

        return $months;
    }

    public function getDeadlinesForMonth(Carbon $date): Collection
    {
        return $this->deadlines->filter(function ($deadline) use ($date) {
            return $deadline->deadline_date->month === $date->month;
        });
    }

    public function getCategoryColor(string $category): string
    {
        return match($category) {
            'iva' => 'bg-blue-500',
            'irpf' => 'bg-green-500',
            'retenciones' => 'bg-orange-500',
            'sociedades' => 'bg-purple-500',
            default => 'bg-gray-500',
        };
    }
};
?>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach($this->getMonthsData() as $monthData)
        @php
            $month = $monthData['date'];
            $monthDeadlines = $monthData['deadlines'];
        @endphp

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-3 text-center">
                <flux:heading size="sm" class="text-gray-900 dark:text-gray-100">
                    {{ $month->translatedFormat('F') }}
                </flux:heading>
            </div>

            <div class="space-y-1">
                @if($monthDeadlines->isEmpty())
                    <div class="py-4 text-center">
                        <flux:text size="sm" class="text-gray-400 dark:text-gray-500">
                            Sin plazos
                        </flux:text>
                    </div>
                @else
                    @foreach($monthDeadlines->take(5) as $deadline)
                        <div
                            wire:click="$parent.showModel({{ $deadline->taxModel->id }})"
                            class="cursor-pointer rounded p-2 text-xs transition hover:opacity-80 {{ $this->getCategoryColor($deadline->taxModel->category ?? 'otros') }} text-white"
                        >
                            <div class="flex items-center justify-between">
                                <span class="font-semibold">{{ $deadline->taxModel->model_number }}</span>
                                <span>{{ $deadline->deadline_date->format('d') }}</span>
                            </div>
                        </div>
                    @endforeach

                    @if($monthDeadlines->count() > 5)
                        <div class="px-2 py-1 text-center text-xs text-gray-500 dark:text-gray-400">
                            +{{ $monthDeadlines->count() - 5 }} más
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endforeach
</div>
