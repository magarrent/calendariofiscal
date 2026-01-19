@props(['deadlines', 'currentDate'])

@php
use Carbon\Carbon;
use Carbon\CarbonPeriod;

if (!function_exists('getCalendarWeeksMonth')) {
    function getCalendarWeeksMonth(Carbon $currentDate): array
    {
        $start = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $end = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::MONDAY);

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
}

if (!function_exists('getDeadlinesForDateMonth')) {
    function getDeadlinesForDateMonth($deadlines, Carbon $date)
    {
        $filtered = $deadlines->filter(function ($deadline) use ($date) {
            return $deadline->deadline_date->isSameDay($date);
        });

        // Group by tax model to avoid showing the same model multiple times
        return $filtered->groupBy('tax_model_id')->map(fn($group) => $group->first());
    }
}

if (!function_exists('getCategoryColorMonth')) {
    function getCategoryColorMonth(string $category): string
    {
        return match($category) {
            'iva' => 'bg-blue-500',
            'irpf' => 'bg-green-500',
            'retenciones' => 'bg-orange-500',
            'sociedades' => 'bg-purple-500',
            default => 'bg-gray-500',
        };
    }
}

if (!function_exists('isModelCompletedMonth')) {
    function isModelCompletedMonth(int $taxModelId, int $year): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->hasCompletedModel(
            \App\Models\TaxModel::find($taxModelId),
            $year
        );
    }
}

$calendarWeeks = getCalendarWeeksMonth($currentDate);
@endphp

<div class="overflow-hidden rounded-lg border border-[#0a3d62]/20">
    {{-- Weekday Headers --}}
    <div class="grid grid-cols-7 gap-px bg-[#0a3d62]/10 dark:bg-gray-700">
        @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $day)
            <div class="bg-[#fdfaf6] p-3 text-center font-semibold text-[#0a3d62] dark:bg-gray-800 dark:text-gray-300" style="font-family: var(--font-serif);">
                {{ $day }}
            </div>
        @endforeach
    </div>

    {{-- Calendar Grid --}}
    <div class="grid grid-cols-7 gap-px bg-[#0a3d62]/10 dark:bg-gray-700">
        @foreach($calendarWeeks as $week)
            @foreach($week as $date)
                @php
                    $isCurrentMonth = $date->month === $currentDate->month;
                    $isToday = $date->isToday();
                    $dayDeadlines = getDeadlinesForDateMonth($deadlines, $date);
                @endphp

                <div class="min-h-[100px] bg-[#fdfaf6] p-2 dark:bg-gray-800 {{ !$isCurrentMonth ? 'opacity-40' : '' }}">
                    <div class="mb-1 flex items-center justify-between">
                        <span class="text-sm font-semibold {{ $isToday ? 'flex size-7 items-center justify-center rounded-full bg-[#0a3d62] text-white font-mono' : ($isCurrentMonth ? 'text-[#0a3d62] dark:text-gray-100' : 'text-gray-400') }}">
                            {{ $date->day }}
                        </span>
                    </div>

                    @if($dayDeadlines->isNotEmpty())
                        <div class="space-y-1">
                            @foreach($dayDeadlines->take(3) as $deadline)
                                @php
                                    $taxModel = $deadline->taxModel;
                                    $isCompleted = isModelCompletedMonth($taxModel->id, $currentDate->year);
                                    $hasConditions = !empty($deadline->conditions);
                                @endphp
                                <div
                                    wire:click="showModel({{ $taxModel->id }})"
                                    class="cursor-pointer rounded px-2 py-1 text-xs {{ $isCompleted ? 'bg-green-600' : 'bg-[#0a3d62]' }} text-white"
                                >
                                    <div class="flex items-center justify-between gap-1">
                                        <div class="flex items-center gap-1">
                                            <span class="font-mono font-bold">{{ $taxModel->model_number }}</span>
                                            @if($hasConditions)
                                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="#f39c12" stroke-width="1.5">
                                                    <circle cx="5" cy="5" r="4"/>
                                                    <path d="M5 2.5v2M5 6.5v0.5"/>
                                                </svg>
                                            @endif
                                        </div>
                                        @if($isCompleted)
                                            <flux:icon.check class="size-3" />
                                        @endif
                                    </div>
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
