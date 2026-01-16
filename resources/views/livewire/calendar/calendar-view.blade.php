<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <flux:heading size="xl" class="text-gray-900 dark:text-white">Calendario Fiscal 2026</flux:heading>

                {{-- View Switcher --}}
                <div class="flex flex-wrap gap-2">
                    <flux:button wire:click="$set('view', 'day')" :variant="$view === 'day' ? 'primary' : 'ghost'" size="sm">Día</flux:button>
                    <flux:button wire:click="$set('view', 'week')" :variant="$view === 'week' ? 'primary' : 'ghost'" size="sm">Semana</flux:button>
                    <flux:button wire:click="$set('view', 'month')" :variant="$view === 'month' ? 'primary' : 'ghost'" size="sm">Mes</flux:button>
                    <flux:button wire:click="$set('view', 'list')" :variant="$view === 'list' ? 'primary' : 'ghost'" size="sm">Lista</flux:button>
                    <flux:button wire:click="$set('view', 'timeline')" :variant="$view === 'timeline' ? 'primary' : 'ghost'" size="sm">Línea</flux:button>
                    <flux:button wire:click="$set('view', 'year')" :variant="$view === 'year' ? 'primary' : 'ghost'" size="sm">Año</flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-4">
            {{-- Filters Sidebar --}}
            <div class="lg:col-span-1">
                <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <flux:heading size="lg">Filtros</flux:heading>
                        @if(!empty($categories) || !empty($frequencies) || !empty($companyTypes) || !empty($tags) || $proximity)
                            <flux:button wire:click="clearFilters" variant="ghost" size="sm">Limpiar</flux:button>
                        @endif
                    </div>

                    <div class="space-y-6">
                        {{-- Category Filter --}}
                        <div>
                            <flux:heading size="sm" class="mb-2">Categoría</flux:heading>
                            <div class="space-y-2">
                                @foreach($availableCategories as $category)
                                    <flux:checkbox
                                        wire:model.live="categories"
                                        value="{{ $category }}"
                                        :label="ucfirst($category)"
                                    />
                                @endforeach
                            </div>
                        </div>

                        <flux:separator />

                        {{-- Frequency Filter --}}
                        <div>
                            <flux:heading size="sm" class="mb-2">Periodicidad</flux:heading>
                            <div class="space-y-2">
                                @foreach($availableFrequencies as $frequency)
                                    <flux:checkbox
                                        wire:model.live="frequencies"
                                        value="{{ $frequency }}"
                                        :label="ucfirst($frequency)"
                                    />
                                @endforeach
                            </div>
                        </div>

                        <flux:separator />

                        {{-- Company Type Filter --}}
                        <div>
                            <flux:heading size="sm" class="mb-2">Tipo de Empresa</flux:heading>
                            <div class="space-y-2">
                                @foreach($availableCompanyTypes as $type)
                                    <flux:checkbox
                                        wire:model.live="companyTypes"
                                        value="{{ $type }}"
                                        :label="match($type) {
                                            'autonomo' => 'Autónomo',
                                            'pyme' => 'PYME',
                                            'gran_empresa' => 'Gran Empresa',
                                            default => ucfirst($type)
                                        }"
                                    />
                                @endforeach
                            </div>
                        </div>

                        <flux:separator />

                        {{-- Proximity Filter --}}
                        <div>
                            <flux:heading size="sm" class="mb-2">Próximos</flux:heading>
                            <flux:select wire:model.live="proximity" placeholder="Seleccionar plazo">
                                <option value="">Todos</option>
                                <option value="next_7_days">Próximos 7 días</option>
                                <option value="next_30_days">Próximos 30 días</option>
                                <option value="next_60_days">Próximos 60 días</option>
                                <option value="next_90_days">Próximos 90 días</option>
                            </flux:select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Calendar Content --}}
            <div class="lg:col-span-3">
                <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                    {{-- Navigation --}}
                    <div class="mb-6 flex items-center justify-between">
                        <flux:button wire:click="previousPeriod" variant="ghost" icon="chevron-left">Anterior</flux:button>

                        <div class="text-center">
                            <flux:heading size="lg">
                                @if($view === 'day')
                                    {{ $currentDate->translatedFormat('d F Y') }}
                                @elseif($view === 'week')
                                    Semana {{ $currentDate->weekOfYear }} - {{ $currentDate->translatedFormat('F Y') }}
                                @elseif($view === 'month')
                                    {{ $currentDate->translatedFormat('F Y') }}
                                @elseif($view === 'year')
                                    {{ $currentDate->year }}
                                @else
                                    {{ $currentDate->translatedFormat('Y') }}
                                @endif
                            </flux:heading>
                        </div>

                        <flux:button wire:click="nextPeriod" variant="ghost" icon-trailing="chevron-right">Siguiente</flux:button>
                    </div>

                    <div class="mb-4 text-center">
                        <flux:button wire:click="today" variant="primary" size="sm">Hoy</flux:button>
                    </div>

                    {{-- Deadlines List --}}
                    <div class="space-y-4">
                        @forelse($deadlines as $deadline)
                            <div
                                wire:key="deadline-{{ $deadline->id }}"
                                wire:click="showModel({{ $deadline->taxModel->id }})"
                                class="cursor-pointer rounded-lg border border-gray-200 p-4 transition hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:border-gray-600 dark:hover:bg-gray-800"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <flux:heading size="sm">{{ $deadline->taxModel->name }}</flux:heading>
                                        <flux:text class="mt-1">
                                            <flux:badge variant="primary">{{ ucfirst($deadline->taxModel->category) }}</flux:badge>
                                            <flux:badge class="ml-2">{{ ucfirst($deadline->taxModel->frequency) }}</flux:badge>
                                        </flux:text>
                                        @if($deadline->notes)
                                            <flux:text class="mt-2 text-sm">{{ $deadline->notes }}</flux:text>
                                        @endif
                                    </div>
                                    <div class="ml-4 text-right">
                                        <flux:text class="font-semibold">
                                            {{ $deadline->deadline_date->translatedFormat('d M Y') }}
                                        </flux:text>
                                        @if($deadline->deadline_time)
                                            <flux:text class="text-sm">{{ $deadline->deadline_time->format('H:i') }}</flux:text>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <flux:text class="text-gray-500">No hay plazos para el período seleccionado</flux:text>
                            </div>
                        @endforelse
                    </div>

                    @if($deadlines->isNotEmpty())
                        <div class="mt-6 text-center">
                            <flux:text class="text-sm text-gray-500">
                                Mostrando {{ $deadlines->count() }} plazo(s)
                            </flux:text>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Model Detail Modal --}}
    @if($selectedModelId)
        <livewire:calendar.model-detail :model-id="$selectedModelId" wire:key="model-{{ $selectedModelId }}" />
    @endif
</div>
