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

    public function getCalendarWeeks(): array
    {
        $start = $this->currentDate->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $end = $this->currentDate->copy()->endOfMonth()->endOfWeek(Carbon::MONDAY);

        $weeks = [];
        $period = CarbonPeriod::create($start, '1 day', $end);

        $currentWeek = [];
        foreach ($period as $date) {
            $currentWeek[] = $date;

            if ($date->dayOfWeek === Carbon::SUNDAY) {
                $weeks[] = $currentWeek;
                $currentWeek = [];
            }
        }

        if (!empty($currentWeek)) {
            $weeks[] = $currentWeek;
        }

        return $weeks;
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
        @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $day)
            <div class="bg-white p-2 text-center text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                {{ $day }}
            </div>
        @endforeach
    </div>

    {{-- Calendar Grid --}}
    <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
        @foreach($this->getCalendarWeeks() as $week)
            @foreach($week as $date)
                @php
                    $isCurrentMonth = $date->month === $this->currentDate->month;
                    $isToday = $date->isToday();
                    $dayDeadlines = $this->getDeadlinesForDate($date);
                @endphp

                <div class="min-h-[100px] bg-white p-2 dark:bg-gray-800 {{ !$isCurrentMonth ? 'opacity-40' : '' }}">
                    <div class="mb-1 flex items-center justify-between">
                        <span class="text-sm font-medium {{ $isToday ? 'flex size-6 items-center justify-center rounded-full bg-blue-500 text-white' : ($isCurrentMonth ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400') }}">
                            {{ $date->day }}
                        </span>
                    </div>

                    @if($dayDeadlines->isNotEmpty())
                        <div class="space-y-1">
                            @foreach($dayDeadlines->take(3) as $deadline)
                                <div
                                    wire:click="$parent.showModel({{ $deadline->taxModel->id }})"
                                    class="cursor-pointer rounded px-2 py-1 text-xs transition hover:opacity-80 {{ $this->getCategoryColor($deadline->taxModel->category ?? 'otros') }} text-white"
                                >
                                    {{ $deadline->taxModel->model_number }}
                                </div>
                            @endforeach

                            @if($dayDeadlines->count() > 3)
                                <div class="px-2 text-xs text-gray-500 dark:text-gray-400">
                                    +{{ $dayDeadlines->count() - 3 }} más
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        @endforeach
    </div>
</div>