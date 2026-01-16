<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <flux:heading size="xl" class="text-gray-900 dark:text-white">Calendario Fiscal 2026</flux:heading>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Guest CTAs --}}
                    @guest
                        <flux:button href="{{ route('login') }}" variant="ghost" size="sm" icon="arrow-right-end-on-rectangle">
                            Iniciar Sesión
                        </flux:button>
                        <flux:button href="{{ route('register') }}" variant="primary" size="sm">
                            Registrarse Gratis
                        </flux:button>
                        <flux:separator vertical class="hidden h-6 sm:block" />
                    @endguest

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
    </div>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-4">
            {{-- Filters Sidebar --}}
            <div class="lg:col-span-1">
                <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:heading size="lg">Filtros</flux:heading>
                            @guest
                                <flux:tooltip content="Regístrate para usar filtros avanzados" position="right" toggleable>
                                    <flux:icon.lock-closed class="size-4 text-gray-400" />
                                </flux:tooltip>
                            @endguest
                        </div>
                        @if(!empty($categories) || !empty($frequencies) || !empty($companyTypes) || !empty($tags) || $proximity)
                            <flux:button wire:click="clearFilters" variant="ghost" size="sm">Limpiar</flux:button>
                        @endif
                    </div>

                    @guest
                        <div class="mb-4 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                            <flux:text class="mb-2 text-sm font-semibold text-blue-900 dark:text-blue-100">
                                Desbloquea Filtros Avanzados
                            </flux:text>
                            <flux:text class="mb-3 text-xs text-blue-700 dark:text-blue-300">
                                Regístrate gratis para filtrar por categoría, periodicidad, tipo de empresa y más.
                            </flux:text>
                            <flux:button href="{{ route('register') }}" variant="primary" size="xs" class="w-full">
                                Registrarse Ahora
                            </flux:button>
                        </div>
                    @endguest

                    <div class="space-y-6" @guest class="pointer-events-none opacity-50" @endguest>
                        {{-- Category Filter --}}
                        <div class="relative">
                            <flux:heading size="sm" class="mb-2">Categoría</flux:heading>
                            <div class="space-y-2">
                                @foreach($availableCategories as $category)
                                    <flux:checkbox
                                        wire:model.live="categories"
                                        value="{{ $category }}"
                                        :label="ucfirst($category)"
                                        :disabled="!$this->canUseFilters()"
                                    />
                                @endforeach
                            </div>
                        </div>

                        <flux:separator />

                        {{-- Frequency Filter --}}
                        <div class="relative">
                            <flux:heading size="sm" class="mb-2">Periodicidad</flux:heading>
                            <div class="space-y-2">
                                @foreach($availableFrequencies as $frequency)
                                    <flux:checkbox
                                        wire:model.live="frequencies"
                                        value="{{ $frequency }}"
                                        :label="ucfirst($frequency)"
                                        :disabled="!$this->canUseFilters()"
                                    />
                                @endforeach
                            </div>
                        </div>

                        <flux:separator />

                        {{-- Company Type Filter --}}
                        <div class="relative">
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
                                        :disabled="!$this->canUseFilters()"
                                    />
                                @endforeach
                            </div>
                        </div>

                        <flux:separator />

                        {{-- Proximity Filter --}}
                        <div class="relative">
                            <flux:heading size="sm" class="mb-2">Próximos</flux:heading>
                            <flux:select wire:model.live="proximity" placeholder="Seleccionar plazo" :disabled="!$this->canUseFilters()">
                                <option value="">Todos</option>
                                <option value="next_7_days">Próximos 7 días</option>
                                <option value="next_30_days">Próximos 30 días</option>
                                <option value="next_60_days">Próximos 60 días</option>
                                <option value="next_90_days">Próximos 90 días</option>
                            </flux:select>
                        </div>

                        @auth
                            <flux:separator />

                            {{-- Completion Filter --}}
                            <div>
                                <flux:heading size="sm" class="mb-2">Estado</flux:heading>
                                <flux:checkbox
                                    wire:model.live="showOnlyIncomplete"
                                    label="Solo mostrar pendientes"
                                />
                            </div>
                        @endauth

                        @guest
                            <flux:separator />

                            {{-- Completion Filter (Locked) --}}
                            <div class="relative">
                                <flux:heading size="sm" class="mb-2">Estado</flux:heading>
                                <flux:checkbox
                                    label="Solo mostrar pendientes"
                                    disabled
                                />
                            </div>
                        @endguest
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

                    <div class="mb-4 flex flex-col items-center gap-2 sm:flex-row sm:justify-center">
                        <flux:button wire:click="today" variant="primary" size="sm">Hoy</flux:button>

                        @if($deadlines->isNotEmpty())
                            <flux:separator vertical class="hidden h-6 sm:block" />
                            <div class="flex flex-wrap items-center justify-center gap-2">
                                @auth
                                    <flux:button wire:click="exportCsv" variant="ghost" size="sm" icon="arrow-down-tray">
                                        Exportar CSV
                                    </flux:button>
                                    <flux:button wire:click="exportExcel" variant="ghost" size="sm" icon="arrow-down-tray">
                                        Exportar Excel
                                    </flux:button>
                                    <flux:button wire:click="exportIcal" variant="ghost" size="sm" icon="calendar">
                                        Exportar iCal
                                    </flux:button>
                                @endauth

                                @guest
                                    <flux:tooltip content="Regístrate para exportar datos" position="bottom" toggleable>
                                        <div class="relative inline-block">
                                            <flux:button variant="ghost" size="sm" icon="arrow-down-tray" disabled class="opacity-50">
                                                Exportar CSV
                                            </flux:button>
                                            <flux:icon.lock-closed class="pointer-events-none absolute right-2 top-1/2 size-3 -translate-y-1/2 text-gray-400" />
                                        </div>
                                    </flux:tooltip>
                                    <flux:tooltip content="Regístrate para exportar datos" position="bottom" toggleable>
                                        <div class="relative inline-block">
                                            <flux:button variant="ghost" size="sm" icon="arrow-down-tray" disabled class="opacity-50">
                                                Exportar Excel
                                            </flux:button>
                                            <flux:icon.lock-closed class="pointer-events-none absolute right-2 top-1/2 size-3 -translate-y-1/2 text-gray-400" />
                                        </div>
                                    </flux:tooltip>
                                    <flux:tooltip content="Regístrate para exportar datos" position="bottom" toggleable>
                                        <div class="relative inline-block">
                                            <flux:button variant="ghost" size="sm" icon="calendar" disabled class="opacity-50">
                                                Exportar iCal
                                            </flux:button>
                                            <flux:icon.lock-closed class="pointer-events-none absolute right-2 top-1/2 size-3 -translate-y-1/2 text-gray-400" />
                                        </div>
                                    </flux:tooltip>
                                @endguest
                            </div>
                        @endif
                    </div>

                    {{-- Deadlines List --}}
                    <div class="space-y-4">
                        @forelse($deadlines as $deadline)
                            @php
                                $isCompleted = $this->isModelCompleted($deadline->taxModel->id);
                            @endphp
                            <div
                                wire:key="deadline-{{ $deadline->id }}"
                                wire:click="showModel({{ $deadline->taxModel->id }})"
                                class="cursor-pointer rounded-lg border p-4 transition {{ $isCompleted ? 'border-green-200 bg-green-50/50 hover:border-green-300 hover:bg-green-50 dark:border-green-800 dark:bg-green-900/20 dark:hover:border-green-700 dark:hover:bg-green-900/30' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:border-gray-600 dark:hover:bg-gray-800' }}"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <flux:heading size="sm" class="{{ $isCompleted ? 'text-green-900 dark:text-green-100' : '' }}">
                                                {{ $deadline->taxModel->name }}
                                            </flux:heading>
                                            @if($isCompleted)
                                                <flux:badge variant="success" size="sm">
                                                    <flux:icon.check class="mr-1 size-3" />
                                                    Completado
                                                </flux:badge>
                                            @endif
                                        </div>
                                        <flux:text class="mt-1">
                                            <flux:badge variant="primary">{{ ucfirst($deadline->taxModel->category) }}</flux:badge>
                                            <flux:badge class="ml-2">{{ ucfirst($deadline->taxModel->frequency) }}</flux:badge>
                                        </flux:text>
                                        @if($deadline->notes)
                                            <flux:text class="mt-2 text-sm">{{ $deadline->notes }}</flux:text>
                                        @endif
                                    </div>
                                    <div class="ml-4 text-right">
                                        <flux:text class="font-semibold {{ $isCompleted ? 'text-green-900 dark:text-green-100' : '' }}">
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
