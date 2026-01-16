<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Models\TaxModel;
use App\Models\Deadline;
use Carbon\Carbon;

new class extends Component
{
    #[Url(keep: true)]
    public string $view = 'month';

    #[Url(keep: true)]
    public string $date = '';

    #[Url(keep: true)]
    public array $categories = [];

    #[Url(keep: true)]
    public array $frequencies = [];

    #[Url(keep: true)]
    public ?string $companyType = null;

    #[Url(keep: true)]
    public ?string $proximity = null;

    #[Url(keep: true)]
    public bool $favoritesOnly = false;

    public ?int $selectedModelId = null;

    public function mount(): void
    {
        if (empty($this->date)) {
            $this->date = now()->format('Y-m-d');
        }
    }

    #[Computed]
    public function currentDate()
    {
        return Carbon::parse($this->date);
    }

    #[Computed]
    public function deadlines()
    {
        $query = Deadline::with('taxModel')
            ->whereHas('taxModel')
            ->byYear($this->currentDate->year);

        $range = $this->getDateRange();
        $query->byDateRange($range['start'], $range['end']);

        if (!empty($this->categories)) {
            $query->whereHas('taxModel', function ($q) {
                $q->whereIn('category', $this->categories);
            });
        }

        if (!empty($this->frequencies)) {
            $query->whereHas('taxModel', function ($q) {
                $q->whereIn('frequency', $this->frequencies);
            });
        }

        if ($this->companyType) {
            $query->whereHas('taxModel', function ($q) {
                $q->applicableTo($this->companyType);
            });
        }

        if ($this->proximity) {
            $days = match($this->proximity) {
                '7' => 7,
                '14' => 14,
                '30' => 30,
                '60' => 60,
                '90' => 90,
                default => null,
            };

            if ($days) {
                $query->upcoming($days);
            }
        }

        if ($this->favoritesOnly && auth()->check()) {
            $favoriteIds = auth()->user()->favoriteTaxModels()->pluck('tax_models.id');
            $query->whereHas('taxModel', function ($q) use ($favoriteIds) {
                $q->whereIn('id', $favoriteIds);
            });
        }

        return $query->orderBy('deadline_date')->get();
    }

    public function getDateRange(): array
    {
        $current = $this->currentDate;

        return match($this->view) {
            'day' => [
                'start' => $current->copy()->startOfDay(),
                'end' => $current->copy()->endOfDay(),
            ],
            'week' => [
                'start' => $current->copy()->startOfWeek(),
                'end' => $current->copy()->endOfWeek(),
            ],
            'month' => [
                'start' => $current->copy()->startOfMonth()->startOfWeek(),
                'end' => $current->copy()->endOfMonth()->endOfWeek(),
            ],
            'year' => [
                'start' => $current->copy()->startOfYear(),
                'end' => $current->copy()->endOfYear(),
            ],
            default => [
                'start' => $current->copy()->startOfMonth(),
                'end' => $current->copy()->endOfMonth(),
            ],
        };
    }

    public function previousPeriod(): void
    {
        $current = $this->currentDate;

        $this->date = match($this->view) {
            'day' => $current->subDay()->format('Y-m-d'),
            'week' => $current->subWeek()->format('Y-m-d'),
            'month' => $current->subMonth()->format('Y-m-d'),
            'year' => $current->subYear()->format('Y-m-d'),
            default => $current->subMonth()->format('Y-m-d'),
        };
    }

    public function nextPeriod(): void
    {
        $current = $this->currentDate;

        $this->date = match($this->view) {
            'day' => $current->addDay()->format('Y-m-d'),
            'week' => $current->addWeek()->format('Y-m-d'),
            'month' => $current->addMonth()->format('Y-m-d'),
            'year' => $current->addYear()->format('Y-m-d'),
            default => $current->addMonth()->format('Y-m-d'),
        };
    }

    public function today(): void
    {
        $this->date = now()->format('Y-m-d');
    }

    public function clearFilters(): void
    {
        $this->categories = [];
        $this->frequencies = [];
        $this->companyType = null;
        $this->proximity = null;
        $this->favoritesOnly = false;
    }

    public function showModel(int $modelId): void
    {
        $this->selectedModelId = $modelId;
        $this->dispatch('open-modal', 'model-detail');
    }

    public function closeModal(): void
    {
        $this->selectedModelId = null;
    }
};
?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <flux:heading size="xl" class="mb-2">Calendario Fiscal 2026</flux:heading>
            <flux:text>Consulta todos los modelos y plazos de presentación ante la AEAT</flux:text>
        </div>

        <div class="grid gap-6 lg:grid-cols-4">
            {{-- Filters Sidebar --}}
            <div class="lg:col-span-1">
                <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                    <div class="mb-6 flex items-center justify-between">
                        <flux:heading size="lg">Filtros</flux:heading>
                        @if(!empty($this->categories) || !empty($this->frequencies) || $this->companyType || $this->proximity || $this->favoritesOnly)
                            <flux:button wire:click="clearFilters" variant="ghost" size="sm">
                                Limpiar
                            </flux:button>
                        @endif
                    </div>

                    {{-- Category Filter --}}
                    <div class="mb-4">
                        <flux:field>
                            <flux:label>Categoría</flux:label>
                            <div class="space-y-2">
                                <flux:checkbox wire:model.live="categories" value="iva">IVA</flux:checkbox>
                                <flux:checkbox wire:model.live="categories" value="irpf">IRPF</flux:checkbox>
                                <flux:checkbox wire:model.live="categories" value="retenciones">Retenciones</flux:checkbox>
                                <flux:checkbox wire:model.live="categories" value="sociedades">Sociedades</flux:checkbox>
                                <flux:checkbox wire:model.live="categories" value="otros">Otros</flux:checkbox>
                            </div>
                        </flux:field>
                    </div>

                    <flux:separator class="my-4" />

                    {{-- Frequency Filter --}}
                    <div class="mb-4">
                        <flux:field>
                            <flux:label>Periodicidad</flux:label>
                            <div class="space-y-2">
                                <flux:checkbox wire:model.live="frequencies" value="monthly">Mensual</flux:checkbox>
                                <flux:checkbox wire:model.live="frequencies" value="quarterly">Trimestral</flux:checkbox>
                                <flux:checkbox wire:model.live="frequencies" value="annual">Anual</flux:checkbox>
                                <flux:checkbox wire:model.live="frequencies" value="one-time">Único</flux:checkbox>
                            </div>
                        </flux:field>
                    </div>

                    <flux:separator class="my-4" />

                    {{-- Company Type Filter --}}
                    <div class="mb-4">
                        <flux:field>
                            <flux:label>Tipo de empresa</flux:label>
                            <flux:select wire:model.live="companyType">
                                <option value="">Todos</option>
                                <option value="autonomo">Autónomo</option>
                                <option value="pyme">PYME</option>
                                <option value="large_corp">Gran empresa</option>
                            </flux:select>
                        </flux:field>
                    </div>

                    <flux:separator class="my-4" />

                    {{-- Proximity Filter --}}
                    <div class="mb-4">
                        <flux:field>
                            <flux:label>Próximos</flux:label>
                            <flux:select wire:model.live="proximity">
                                <option value="">Todos los plazos</option>
                                <option value="7">Próximos 7 días</option>
                                <option value="14">Próximos 14 días</option>
                                <option value="30">Próximos 30 días</option>
                                <option value="60">Próximos 60 días</option>
                                <option value="90">Próximos 90 días</option>
                            </flux:select>
                        </flux:field>
                    </div>

                    @auth
                        <flux:separator class="my-4" />
                        <div class="mb-4">
                            <flux:checkbox wire:model.live="favoritesOnly">
                                Solo favoritos
                            </flux:checkbox>
                        </div>
                    @endauth
                </div>
            </div>

            {{-- Calendar Main Area --}}
            <div class="lg:col-span-3">
                <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                    {{-- Calendar Controls --}}
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="previousPeriod" variant="ghost" icon="chevron-left"></flux:button>
                            <flux:button wire:click="today" variant="ghost">Hoy</flux:button>
                            <flux:button wire:click="nextPeriod" variant="ghost" icon="chevron-right"></flux:button>
                            <flux:text class="ml-4 font-semibold">
                                {{ $this->currentDate->format('F Y') }}
                            </flux:text>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <flux:button
                                wire:click="$set('view', 'day')"
                                :variant="$view === 'day' ? 'primary' : 'ghost'"
                                size="sm"
                            >
                                Día
                            </flux:button>
                            <flux:button
                                wire:click="$set('view', 'week')"
                                :variant="$view === 'week' ? 'primary' : 'ghost'"
                                size="sm"
                            >
                                Semana
                            </flux:button>
                            <flux:button
                                wire:click="$set('view', 'month')"
                                :variant="$view === 'month' ? 'primary' : 'ghost'"
                                size="sm"
                            >
                                Mes
                            </flux:button>
                            <flux:button
                                wire:click="$set('view', 'list')"
                                :variant="$view === 'list' ? 'primary' : 'ghost'"
                                size="sm"
                            >
                                Lista
                            </flux:button>
                            <flux:button
                                wire:click="$set('view', 'timeline')"
                                :variant="$view === 'timeline' ? 'primary' : 'ghost'"
                                size="sm"
                            >
                                Línea
                            </flux:button>
                            <flux:button
                                wire:click="$set('view', 'year')"
                                :variant="$view === 'year' ? 'primary' : 'ghost'"
                                size="sm"
                            >
                                Año
                            </flux:button>
                        </div>
                    </div>

                    {{-- Calendar View --}}
                    @if($view === 'month')
                        <livewire:calendar.month-view :deadlines="$this->deadlines" :current-date="$this->currentDate" />
                    @elseif($view === 'week')
                        <livewire:calendar.week-view :deadlines="$this->deadlines" :current-date="$this->currentDate" />
                    @elseif($view === 'day')
                        <livewire:calendar.day-view :deadlines="$this->deadlines" :current-date="$this->currentDate" />
                    @elseif($view === 'list')
                        <livewire:calendar.list-view :deadlines="$this->deadlines" />
                    @elseif($view === 'timeline')
                        <livewire:calendar.timeline-view :deadlines="$this->deadlines" />
                    @elseif($view === 'year')
                        <livewire:calendar.year-view :deadlines="$this->deadlines" :current-date="$this->currentDate" />
                    @endif
                </div>

                {{-- Export Options --}}
                <div class="mt-4 flex gap-2">
                    <flux:button wire:click="$dispatch('export-csv')" variant="outline">
                        <flux:icon.arrow-down-tray class="size-4" />
                        Exportar CSV
                    </flux:button>
                    <flux:button wire:click="$dispatch('export-ical')" variant="outline">
                        <flux:icon.calendar class="size-4" />
                        Exportar iCal
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    {{-- Model Detail Modal --}}
    @if($selectedModelId)
        <livewire:calendar.model-detail :model-id="$selectedModelId" wire:key="model-{{ $selectedModelId }}" />
    @endif
</div>