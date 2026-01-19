@props(['deadlines', 'currentDate'])

@php
use Carbon\Carbon;

// Generate months data
$months = [];
for ($month = 1; $month <= 12; $month++) {
    $date = Carbon::create($currentDate->year, $month, 1);
    $monthDeadlines = $deadlines->filter(function ($deadline) use ($date) {
        return $deadline->deadline_date->month === $date->month;
    });

    // Group by tax model to avoid showing the same model multiple times
    $groupedDeadlines = $monthDeadlines->groupBy('tax_model_id')->map(fn($group) => $group->first());

    $months[] = [
        'date' => $date,
        'deadlines' => $groupedDeadlines,
    ];
}

if (!function_exists('getCategoryColorYear')) {
    function getCategoryColorYear(string $category): string
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

if (!function_exists('isModelCompletedYear')) {
    function isModelCompletedYear(int $taxModelId, int $year): bool
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
@endphp

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach($months as $monthData)
        @php
            $month = $monthData['date'];
            $monthDeadlines = $monthData['deadlines'];
        @endphp

        <div class="rounded-lg documento-card p-4">
            <div class="mb-3 text-center">
                <h3 class="calendar-heading text-xl text-[#0a3d62] dark:text-white">
                    {{ $month->translatedFormat('F') }}
                </h3>
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
                        @php
                            $taxModel = $deadline->taxModel;
                            $isCompleted = isModelCompletedYear($taxModel->id, $currentDate->year);
                            $hasConditions = !empty($deadline->conditions);
                        @endphp
                        <div
                            wire:click="showModel({{ $taxModel->id }})"
                            class="cursor-pointer rounded-lg p-3 text-xs {{ $isCompleted ? 'bg-green-600' : 'bg-[#0a3d62]' }} text-white"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        <span class="font-mono font-bold tracking-wider">{{ $taxModel->model_number }}</span>
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
                                <span class="font-semibold text-sm">{{ $deadline->deadline_date->format('d') }}</span>
                            </div>
                            @if($isCompleted)
                                <div class="mt-1 text-xs font-medium tracking-wide uppercase opacity-90">
                                    ✓ Completado
                                </div>
                            @endif
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
