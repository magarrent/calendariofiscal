@props(['deadlines', 'currentDate'])

@php
    $deadlinesForToday = $deadlines
        ->filter(fn($deadline) => $deadline->deadline_date->isSameDay($currentDate))
        ->groupBy('tax_model_id')
        ->map(fn($group) => $group->first());
@endphp

<div>
    {{-- Day Header --}}
    <div class="mb-6 rounded-lg documento-card p-6">
        <div class="text-center">
            <div class="text-sm font-medium text-[#0a3d62] dark:text-gray-400 uppercase tracking-wider">
                {{ $currentDate->translatedFormat('l') }}
            </div>
            <div class="mt-2 text-5xl font-bold text-[#0a3d62] dark:text-gray-100 font-mono">
                {{ $currentDate->day }}
            </div>
            <div class="mt-2 calendar-heading text-lg text-[#0a3d62] dark:text-gray-400">
                {{ $currentDate->translatedFormat('F Y') }}
            </div>
        </div>
    </div>

    {{-- Deadlines for Today --}}
    <div class="space-y-3">
        @if($deadlinesForToday->isEmpty())
            <div class="rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-gray-700">
                <flux:icon.calendar-days class="mx-auto size-12 text-gray-400" />
                <flux:text class="mt-2 text-gray-500 dark:text-gray-400">
                    No hay plazos para este día
                </flux:text>
            </div>
        @else
            @foreach($deadlinesForToday->sortBy('deadline_date') as $deadline)
                @php
                    $categoryColor = match($deadline->taxModel->category ?? 'otros') {
                        'iva' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'irpf' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'retenciones' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                        'sociedades' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                    };

                    $frequencyLabel = match($deadline->taxModel->frequency) {
                        'monthly' => 'Mensual',
                        'quarterly' => 'Trimestral',
                        'annual' => 'Anual',
                        'one-time' => 'Único',
                        default => $deadline->taxModel->frequency,
                    };
                @endphp

                <div
                    wire:click="showModel({{ $deadline->taxModel->id }})"
                    class="cursor-pointer rounded-lg documento-card p-6"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <flux:badge size="sm" class="{{ $categoryColor }}">
                                    Modelo {{ $deadline->taxModel->model_number }}
                                </flux:badge>

                                <flux:badge size="sm" variant="outline">
                                    {{ $frequencyLabel }}
                                </flux:badge>

                                @if($deadline->conditions)
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
                            </div>

                            <h3 class="calendar-heading text-xl text-[#0a3d62] dark:text-white mt-3">
                                {{ $deadline->taxModel->name }}
                            </h3>

                            @if($deadline->taxModel->description)
                                <flux:text class="mt-2 text-gray-600 dark:text-gray-400">
                                    {{ $deadline->taxModel->description }}
                                </flux:text>
                            @endif

                            @if($deadline->notes)
                                <div class="mt-3 rounded bg-gray-50 p-3 dark:bg-gray-700">
                                    <flux:text size="sm" class="text-gray-600 dark:text-gray-400">
                                        <strong>Notas:</strong> {{ $deadline->notes }}
                                    </flux:text>
                                </div>
                            @endif
                        </div>

                        @auth
                            @if(auth()->user()->hasFavorite($deadline->taxModel))
                                <flux:icon.star class="ml-4 size-6 text-yellow-400" />
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
