@props(['deadlines', 'currentDate'])

@php
use Carbon\Carbon;
use Carbon\CarbonPeriod;

if (!function_exists('getWeekDays')) {
    function getWeekDays(Carbon $currentDate): array
    {
        $start = $currentDate->copy()->startOfWeek(Carbon::MONDAY);
        $end = $currentDate->copy()->endOfWeek(Carbon::MONDAY);

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
        return $deadlines->filter(function ($deadline) use ($date) {
            return $deadline->deadline_date->isSameDay($date);
        });
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

<div class="overflow-hidden">
    {{-- Weekday Headers --}}
    <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
        @foreach($weekDays as $day)
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
        @foreach($weekDays as $date)
            @php
                $isToday = $date->isToday();
                $dayDeadlines = getDeadlinesForDateWeek($deadlines, $date);
            @endphp

            <div class="min-h-[200px] bg-white p-3 dark:bg-gray-800">
                @if($dayDeadlines->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($dayDeadlines as $deadline)
                            @php
                                $taxModel = $deadline->taxModel;
                                $isCompleted = isModelCompletedWeek($taxModel->id, $currentDate->year);
                            @endphp
                            <div
                                wire:click="showModel({{ $taxModel->id }})"
                                class="cursor-pointer rounded px-2 py-2 text-xs transition hover:opacity-80 {{ $isCompleted ? 'bg-green-600' : getCategoryColorWeek($taxModel->category ?? 'otros') }} text-white"
                            >
                                <div class="flex items-center justify-between gap-1">
                                    <div class="font-semibold">{{ $taxModel->model_number }}</div>
                                    @if($isCompleted)
                                        <flux:icon.check class="size-3" />
                                    @endif
                                </div>
                                <div class="mt-1 text-xs opacity-90">{{ Str::limit($taxModel->name, 30) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
