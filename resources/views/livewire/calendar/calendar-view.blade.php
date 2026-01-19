<div class="min-h-screen bg-[#fdfaf6] dark:bg-gray-900">
    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b-2 border-[#0a3d62] dark:border-gray-700">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="calendar-heading text-4xl text-[#0a3d62] dark:text-white">Calendario Fiscal 2026</h1>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Guest CTAs --}}
                    @guest
                        <flux:button href="{{ route('login') }}" variant="ghost" size="sm" icon="arrow-right-end-on-rectangle">
                            Iniciar Sesión
                        </flux:button>
                        <flux:button href="{{ route('register') }}" variant="primary" size="sm">
                            Registrarse Gratis
                        </flux:button>
                    @endguest

                    {{-- Authenticated User Menu --}}
                    @auth
                        <flux:dropdown position="top" align="end">
                            <flux:profile avatar="{{ 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" name="{{ auth()->user()->name }}" />

                            <flux:menu>
                                <flux:menu.item icon="cog-6-tooth" href="{{ route('profile.edit') }}">{{ __('Settings') }}</flux:menu.item>

                                <flux:menu.separator />

                                <flux:menu.item icon="arrow-right-start-on-rectangle" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Log Out') }}
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-4">
            {{-- Filters Sidebar --}}
            <div class="lg:col-span-1">
                <div class="rounded-lg documento-card p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="calendar-heading text-2xl text-[#0a3d62] dark:text-white">Filtros</h2>
                        @if(!empty($categories) || !empty($frequencies) || !empty($companyTypes) || !empty($tags) || $proximity)
                            <flux:button wire:click="clearFilters" variant="ghost" size="sm">Limpiar</flux:button>
                        @endif
                    </div>

                    <div class="space-y-6">
                        {{-- Category Filter --}}
                        <div class="relative">
                            <h3 class="calendar-heading text-lg text-[#0a3d62] dark:text-white mb-2">Categoría</h3>
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
                            <h3 class="calendar-heading text-lg text-[#0a3d62] dark:text-white mb-2">Periodicidad</h3>
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
                            <h3 class="calendar-heading text-lg text-[#0a3d62] dark:text-white mb-2">Tipo de Empresa</h3>
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
                            <h3 class="calendar-heading text-lg text-[#0a3d62] dark:text-white mb-2">Próximos</h3>
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
                                <h3 class="calendar-heading text-lg text-[#0a3d62] dark:text-white mb-2">Estado</h3>
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
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="calendar-heading text-lg text-[#0a3d62] dark:text-white">Estado</h3>
                                    <flux:tooltip content="Regístrate para usar este filtro" position="right" toggleable>
                                        <flux:icon.lock-closed class="size-4 text-gray-400" />
                                    </flux:tooltip>
                                </div>
                                <flux:checkbox
                                    label="Solo mostrar pendientes"
                                    disabled
                                />
                                <flux:text class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                    Regístrate gratis para marcar modelos como completados
                                </flux:text>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>

            {{-- Calendar Content --}}
            <div class="lg:col-span-3">
                <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                    {{-- View Switcher --}}
                    <div class="mb-6 flex justify-center">
                        <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700">
                            <flux:button
                                wire:click="$set('view', 'day')"
                                variant="{{ $view === 'day' ? 'primary' : 'ghost' }}"
                                size="sm"
                                class="rounded-r-none border-r border-gray-200 dark:border-gray-700"
                            >
                                Día
                            </flux:button>
                            <flux:button
                                wire:click="$set('view', 'week')"
                                variant="{{ $view === 'week' ? 'primary' : 'ghost' }}"
                                size="sm"
                                class="rounded-none border-r border-gray-200 dark:border-gray-700"
                            >
                                Semana
                            </flux:button>
                            <flux:button
                                wire:click="$set('view', 'month')"
                                variant="{{ $view === 'month' ? 'primary' : 'ghost' }}"
                                size="sm"
                                class="rounded-none border-r border-gray-200 dark:border-gray-700"
                            >
                                Mes
                            </flux:button>
                            <flux:button
                                wire:click="$set('view', 'year')"
                                variant="{{ $view === 'year' ? 'primary' : 'ghost' }}"
                                size="sm"
                                class="rounded-none border-r border-gray-200 dark:border-gray-700"
                            >
                                Año
                            </flux:button>
                            <flux:button
                                wire:click="$set('view', 'list')"
                                variant="{{ $view === 'list' ? 'primary' : 'ghost' }}"
                                size="sm"
                                class="rounded-l-none"
                            >
                                Lista
                            </flux:button>
                        </div>
                    </div>

                    {{-- Navigation --}}
                    <div class="mb-6 flex items-center justify-between">
                        <flux:button wire:click="previousPeriod" variant="ghost" icon="chevron-left">Anterior</flux:button>

                        <div class="text-center">
                            <flux:heading size="lg">
                                @if($view === 'day')
                                    {{ $currentDate->isoFormat('D [de] MMMM [de] YYYY') }}
                                @elseif($view === 'week')
                                    Semana {{ $currentDate->weekOfYear }} - {{ $currentDate->year }}
                                @elseif($view === 'month')
                                    {{ $currentDate->isoFormat('MMMM YYYY') }}
                                @elseif($view === 'list')
                                    {{ $currentDate->year }}
                                @else
                                    {{ $currentDate->year }}
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

                    {{-- Dynamic Calendar View --}}
                    @if($view === 'day')
                        <x-calendar.day-view :deadlines="$deadlines" :current-date="$currentDate" />
                    @elseif($view === 'week')
                        <x-calendar.week-view :deadlines="$deadlines" :current-date="$currentDate" />
                    @elseif($view === 'month')
                        <x-calendar.month-view :deadlines="$deadlines" :current-date="$currentDate" />
                    @elseif($view === 'list')
                        <x-calendar.list-view :deadlines="$deadlines" :current-date="$currentDate" />
                    @else
                        <x-calendar.year-view :deadlines="$deadlines" :current-date="$currentDate" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Model Detail Modal --}}
    <livewire:calendar.model-detail />
</div>
