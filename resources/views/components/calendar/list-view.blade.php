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
                <flux:heading size="lg" class="mb-3 text-gray-900 dark:text-gray-100">
                    {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
                </flux:heading>

                <div class="space-y-2">
                    @foreach($monthDeadlines as $deadline)
                        @php
                            $isCompleted = isModelCompletedList($deadline->taxModel->id, $currentDate->year);
                        @endphp
                        <div
                            wire:click="showModel({{ $deadline->taxModel->id }})"
                            class="flex cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-4 transition hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-gray-600 dark:hover:bg-gray-700"
                        >
                            <div class="flex items-center space-x-4">
                                <div class="flex flex-col items-center">
                                    <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $deadline->deadline_date->format('d') }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $deadline->deadline_date->translatedFormat('D') }}
                                    </span>
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <flux:badge
                                            size="sm"
                                            class="{{ getCategoryColorList($deadline->taxModel->category ?? 'otros') }}"
                                        >
                                            Modelo {{ $deadline->taxModel->model_number }}
                                        </flux:badge>

                                        <flux:badge size="sm" variant="outline">
                                            {{ getFrequencyLabelList($deadline->taxModel->frequency) }}
                                        </flux:badge>

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

                                    @if($deadline->taxModel->description)
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
