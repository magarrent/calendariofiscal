@props(['deadlines', 'currentDate'])

@php
use Carbon\Carbon;
use Carbon\CarbonPeriod;

if (!function_exists('getWeekDays')) {
    function getWeekDays(Carbon $currentDate): array
    {
        $start = $currentDate->copy()->startOfWeek(Carbon::MONDAY);
        $end = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);

        $days = [];
        $period = CarbonPeriod::create($start, '1 day', $end);

        foreach ($period as $date) {
            $days[] = $date;
        }

        return $days;
    }
}

if (!function_exists('getDeadlinesForDateWeek')) {
    function getDeadlinesForDateWeek($deadlines, Carbon $date)
    {
        $filtered = $deadlines->filter(function ($deadline) use ($date) {
            return $deadline->deadline_date->isSameDay($date);
        });

        // Group by tax model to avoid showing the same model multiple times
        return $filtered->groupBy('tax_model_id')->map(fn($group) => $group->first());
    }
}

if (!function_exists('getCategoryColorWeek')) {
    function getCategoryColorWeek(string $category): string
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

if (!function_exists('isModelCompletedWeek')) {
    function isModelCompletedWeek(int $taxModelId, int $year): bool
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

$weekDays = getWeekDays($currentDate);
@endphp

<div class="overflow-hidden rounded-lg border border-[#0a3d62]/20">
    {{-- Weekday Headers --}}
    <div class="grid grid-cols-7 gap-px bg-[#0a3d62]/10 dark:bg-gray-700">
        @foreach($weekDays as $day)
            <div class="bg-[#fdfaf6] p-3 text-center dark:bg-gray-800">
                <div class="text-sm font-semibold text-[#0a3d62] dark:text-gray-300" style="font-family: var(--font-serif);">
                    {{ $day->translatedFormat('D') }}
                </div>
                <div class="mt-1 text-2xl font-bold font-mono {{ $day->isToday() ? 'text-[#c0392b]' : 'text-[#0a3d62] dark:text-gray-100' }}">
                    {{ $day->day }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Calendar Grid --}}
    <div class="grid grid-cols-7 gap-px bg-[#0a3d62]/10 dark:bg-gray-700">
        @foreach($weekDays as $date)
            @php
                $isToday = $date->isToday();
                $dayDeadlines = getDeadlinesForDateWeek($deadlines, $date);
            @endphp

            <div class="min-h-[200px] bg-[#fdfaf6] p-3 dark:bg-gray-800">
                @if($dayDeadlines->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($dayDeadlines as $deadline)
                            @php
                                $taxModel = $deadline->taxModel;
                                $isCompleted = isModelCompletedWeek($taxModel->id, $currentDate->year);
                                $hasConditions = !empty($deadline->conditions);
                            @endphp
                            <div
                                wire:click="showModel({{ $taxModel->id }})"
                                class="cursor-pointer rounded-lg px-3 py-2 text-xs {{ $isCompleted ? 'bg-green-600' : 'bg-[#0a3d62]' }} text-white"
                            >
                                <div class="flex items-center justify-between gap-1">
                                    <div class="flex items-center gap-1">
                                        <div class="font-mono font-bold tracking-wider">{{ $taxModel->model_number }}</div>
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
                                <div class="mt-1 text-xs opacity-90 font-medium">{{ Str::limit($taxModel->name, 30) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
