@props(['deadlines', 'currentDate'])

@php
use Carbon\Carbon;

if (!function_exists('getCategoryColorList')) {
    function getCategoryColorList(string $category): string
    {
        return match($category) {
            'iva' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'irpf' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'retenciones' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'sociedades' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }
}

if (!function_exists('getFrequencyLabelList')) {
    function getFrequencyLabelList(string $frequency): string
    {
        return match($frequency) {
            'monthly' => 'Mensual',
            'quarterly' => 'Trimestral',
            'annual' => 'Anual',
            'one-time' => 'Único',
            default => $frequency,
        };
    }
}

if (!function_exists('isModelCompletedList')) {
    function isModelCompletedList(int $taxModelId, int $year): bool
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

<div class="space-y-2">
    @if($deadlines->isEmpty())
        <div class="rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-gray-700">
            <flux:icon.calendar-days class="mx-auto size-12 text-gray-400" />
            <flux:text class="mt-2 text-gray-500 dark:text-gray-400">
                No hay plazos para los filtros seleccionados
            </flux:text>
        </div>
    @else
        @foreach($deadlines->groupBy(fn($d) => $d->deadline_date->format('Y-m')) as $month => $monthDeadlines)
            <div class="mb-6">
                <h3 class="calendar-heading text-2xl text-[#0a3d62] dark:text-white mb-4">
                    {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
                </h3>

                <div class="space-y-3">
                    @foreach($monthDeadlines->groupBy(fn($d) => $d->taxModel->id . '_' . $d->deadline_date->format('Y-m-d')) as $groupedDeadlines)
                        @php
                            $deadline = $groupedDeadlines->first();
                            $isCompleted = isModelCompletedList($deadline->taxModel->id, $currentDate->year);
                            $hasMultiplePeriods = $groupedDeadlines->count() > 1;
                        @endphp
                        <div
                            wire:click="showModel({{ $deadline->taxModel->id }})"
                            class="documento-card flex cursor-pointer items-center justify-between rounded-lg p-5"
                        >
                            <div class="flex items-center space-x-4">
                                <div class="flex flex-col items-center rounded-lg bg-[#0a3d62] p-3 text-white">
                                    <span class="text-3xl font-bold font-mono">
                                        {{ $deadline->deadline_date->format('d') }}
                                    </span>
                                    <span class="text-xs uppercase tracking-wide font-medium">
                                        {{ $deadline->deadline_date->translatedFormat('D') }}
                                    </span>
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-bold font-mono">
                                            Modelo {{ $deadline->taxModel->model_number }}
                                        </span>

                                        <flux:badge size="sm" variant="outline">
                                            {{ getFrequencyLabelList($deadline->taxModel->frequency) }}
                                        </flux:badge>

                                        @if($hasMultiplePeriods)
                                            <flux:badge size="sm" variant="outline">
                                                {{ $groupedDeadlines->count() }} períodos
                                            </flux:badge>
                                        @endif

                                        @if($groupedDeadlines->first(fn($d) => !empty($d->conditions)))
                                            <flux:badge size="sm" variant="warning">
                                                <flux:icon.information-circle class="size-3" />
                                                Condiciones
                                            </flux:badge>
                                        @endif

                                        @if($deadline->deadline_time)
                                            <flux:badge size="sm" variant="outline">
                                                <flux:icon.clock class="size-3" />
                                                {{ $deadline->deadline_time->format('H:i') }}
                                            </flux:badge>
                                        @endif

                                        @if($isCompleted)
                                            <flux:badge size="sm" variant="success">
                                                <flux:icon.check class="size-3" />
                                                Completado
                                            </flux:badge>
                                        @endif
                                    </div>

                                    <flux:heading size="sm" class="mt-1 text-gray-900 dark:text-gray-100">
                                        {{ $deadline->taxModel->name }}
                                    </flux:heading>

                                    @if($deadline->details)
                                        <flux:text size="sm" class="mt-1 text-gray-600 dark:text-gray-400">
                                            {{ Str::limit($deadline->details, 120) }}
                                        </flux:text>
                                    @elseif($deadline->taxModel->description)
                                        <flux:text size="sm" class="mt-1 text-gray-600 dark:text-gray-400">
                                            {{ Str::limit($deadline->taxModel->description, 100) }}
                                        </flux:text>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($deadline->deadline_date->isPast() && !$isCompleted)
                                    <flux:badge variant="danger" size="sm">
                                        Vencido
                                    </flux:badge>
                                @elseif($deadline->deadline_date->diffInDays(now()) <= 7 && !$deadline->deadline_date->isPast())
                                    <flux:badge variant="warning" size="sm">
                                        Próximo
                                    </flux:badge>
                                @endif

                                @auth
                                    @if(auth()->user()->hasFavorite($deadline->taxModel))
                                        <flux:icon.star class="size-5 text-yellow-400" />
                                    @endif
                                @endauth

                                <flux:icon.chevron-right class="size-5 text-gray-400" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
