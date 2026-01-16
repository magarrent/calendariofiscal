<?php

use Livewire\Component;
use Illuminate\Support\Collection;

new class extends Component
{
    public Collection $deadlines;

    public function mount(Collection $deadlines): void
    {
        $this->deadlines = $deadlines;
    }

    public function getCategoryColor(string $category): string
    {
        return match($category) {
            'iva' => 'border-blue-500 bg-blue-50 dark:bg-blue-900/20',
            'irpf' => 'border-green-500 bg-green-50 dark:bg-green-900/20',
            'retenciones' => 'border-orange-500 bg-orange-50 dark:bg-orange-900/20',
            'sociedades' => 'border-purple-500 bg-purple-50 dark:bg-purple-900/20',
            default => 'border-gray-500 bg-gray-50 dark:bg-gray-900/20',
        };
    }

    public function getCategoryBadgeColor(string $category): string
    {
        return match($category) {
            'iva' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'irpf' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'retenciones' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'sociedades' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    public function getFrequencyLabel(string $frequency): string
    {
        return match($frequency) {
            'monthly' => 'Mensual',
            'quarterly' => 'Trimestral',
            'annual' => 'Anual',
            'one-time' => 'Único',
            default => $frequency,
        };
    }
};
?>

<div class="relative">
    @if($deadlines->isEmpty())
        <div class="rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-gray-700">
            <flux:text class="text-gray-500 dark:text-gray-400">
                No hay plazos para los filtros seleccionados
            </flux:text>
        </div>
    @else
        <div class="space-y-6">
            @foreach($deadlines as $index => $deadline)
                @php
                    $isPast = $deadline->deadline_date->isPast();
                    $isUpcoming = $deadline->deadline_date->diffInDays(now()) <= 7 && !$isPast;
                @endphp

                <div class="relative flex gap-4">
                    {{-- Timeline Line --}}
                    <div class="flex flex-col items-center">
                        <div class="flex size-10 items-center justify-center rounded-full {{ $isPast ? 'bg-gray-300 dark:bg-gray-600' : 'bg-blue-500' }}">
                            <flux:icon.calendar-days class="size-5 text-white" />
                        </div>
                        @if($index < $deadlines->count() - 1)
                            <div class="h-full w-0.5 {{ $isPast ? 'bg-gray-200 dark:bg-gray-700' : 'bg-blue-200 dark:bg-blue-800' }}"></div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 pb-6">
                        <div class="mb-2">
                            <flux:text size="sm" class="font-semibold text-gray-500 dark:text-gray-400">
                                {{ $deadline->deadline_date->translatedFormat('l, j \d\e F \d\e Y') }}
                            </flux:text>
                        </div>

                        <div
                            wire:click="$parent.showModel({{ $deadline->taxModel->id }})"
                            class="cursor-pointer rounded-lg border-l-4 p-4 transition hover:shadow-md {{ $this->getCategoryColor($deadline->taxModel->category ?? 'otros') }}"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <flux:badge
                                            size="sm"
                                            class="{{ $this->getCategoryBadgeColor($deadline->taxModel->category ?? 'otros') }}"
                                        >
                                            Modelo {{ $deadline->taxModel->model_number }}
                                        </flux:badge>

                                        <flux:badge size="sm" variant="outline">
                                            {{ $this->getFrequencyLabel($deadline->taxModel->frequency) }}
                                        </flux:badge>

                                        @if($isPast)
                                            <flux:badge variant="danger" size="sm">
                                                Vencido
                                            </flux:badge>
                                        @elseif($isUpcoming)
                                            <flux:badge variant="warning" size="sm">
                                                Próximo
                                            </flux:badge>
                                        @endif

                                        @if($deadline->deadline_time)
                                            <flux:badge size="sm" variant="outline">
                                                <flux:icon.clock class="size-3" />
                                                {{ $deadline->deadline_time->format('H:i') }}
                                            </flux:badge>
                                        @endif
                                    </div>

                                    <flux:heading size="lg" class="mt-2">
                                        {{ $deadline->taxModel->name }}
                                    </flux:heading>

                                    @if($deadline->taxModel->description)
                                        <flux:text class="mt-2 text-gray-600 dark:text-gray-400">
                                            {{ $deadline->taxModel->description }}
                                        </flux:text>
                                    @endif

                                    @if($deadline->notes)
                                        <div class="mt-3 rounded bg-white/50 p-3 dark:bg-gray-800/50">
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
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
