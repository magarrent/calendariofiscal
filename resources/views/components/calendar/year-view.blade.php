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
    $months[] = [
        'date' => $date,
        'deadlines' => $monthDeadlines,
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
                        @php
                            $taxModel = $deadline->taxModel;
                            $isCompleted = isModelCompletedYear($taxModel->id, $currentDate->year);
                        @endphp
                        <div
                            wire:click="showModel({{ $taxModel->id }})"
                            class="cursor-pointer rounded p-2 text-xs transition hover:opacity-80 {{ $isCompleted ? 'bg-green-600' : getCategoryColorYear($taxModel->category ?? 'otros') }} text-white"
                        >
                            <div class="flex items-center justify-between gap-1">
                                <div class="flex items-center gap-1">
                                    <span class="font-semibold">{{ $taxModel->model_number }}</span>
                                    @if($isCompleted)
                                        <flux:icon.check class="size-3" />
                                    @endif
                                </div>
                                <span>{{ $deadline->deadline_date->format('d') }}</span>
                            </div>
                            @if($isCompleted)
                                <div class="mt-0.5 text-xs opacity-90">
                                    Completado
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
