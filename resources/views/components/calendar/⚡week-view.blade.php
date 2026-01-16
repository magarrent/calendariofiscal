<?php

use Livewire\Component;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

new class extends Component
{
    public Collection $deadlines;
    public Carbon $currentDate;

    public function mount(Collection $deadlines, Carbon $currentDate): void
    {
        $this->deadlines = $deadlines;
        $this->currentDate = $currentDate;
    }

    public function getWeekDays(): array
    {
        $start = $this->currentDate->copy()->startOfWeek(Carbon::MONDAY);
        $end = $this->currentDate->copy()->endOfWeek(Carbon::MONDAY);

        $days = [];
        $period = CarbonPeriod::create($start, '1 day', $end);

        foreach ($period as $date) {
            $days[] = $date;
        }

        return $days;
    }

    public function getDeadlinesForDate(Carbon $date): Collection
    {
        return $this->deadlines->filter(function ($deadline) use ($date) {
            return $deadline->deadline_date->isSameDay($date);
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

<div class="overflow-hidden">
    {{-- Weekday Headers --}}
    <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
        @foreach($this->getWeekDays() as $day)
            <div class="bg-white p-3 text-center dark:bg-gray-800">
                <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    {{ $day->translatedFormat('D') }}
                </div>
                <div class="mt-1 text-2xl font-bold {{ $day->isToday() ? 'text-blue-500' : 'text-gray-900 dark:text-gray-100' }}">
                    {{ $day->day }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Calendar Grid --}}
    <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
        @foreach($this->getWeekDays() as $date)
            @php
                $isToday = $date->isToday();
                $dayDeadlines = $this->getDeadlinesForDate($date);
            @endphp

            <div class="min-h-[200px] bg-white p-3 dark:bg-gray-800">
                @if($dayDeadlines->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($dayDeadlines as $deadline)
                            <div
                                wire:click="$parent.showModel({{ $deadline->taxModel->id }})"
                                class="cursor-pointer rounded px-2 py-2 text-xs transition hover:opacity-80 {{ $this->getCategoryColor($deadline->taxModel->category ?? 'otros') }} text-white"
                            >
                                <div class="font-semibold">{{ $deadline->taxModel->model_number }}</div>
                                <div class="mt-1 text-xs opacity-90">{{ Str::limit($deadline->taxModel->name, 30) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>