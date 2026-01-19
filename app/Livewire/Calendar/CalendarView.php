<?php

namespace App\Livewire\Calendar;

use App\Exports\DeadlinesExport;
use App\Models\Deadline;
use App\Models\TaxModel;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Session;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CalendarView extends Component
{
    #[Session]
    public string $view = 'month';

    #[Session]
    public array $categories = [];

    #[Session]
    public array $frequencies = [];

    #[Session]
    public array $companyTypes = [];

    #[Session]
    public array $tags = [];

    #[Session]
    public ?string $proximity = null;

    #[Session]
    public bool $showOnlyIncomplete = false;

    public Carbon $currentDate;

    public int $year = 2026;

    public ?int $selectedModelId = null;

    public function mount(): void
    {
        $this->currentDate = Carbon::now();
        $this->year = (int) now()->year;
    }

    public function showModel(int $modelId): void
    {
        $this->selectedModelId = $modelId;
        $this->dispatch('load-model', modelId: $modelId);
        Flux::modal('model-detail')->show();
    }

    public function isModelCompleted(int $taxModelId): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return auth()->user()->hasCompletedModel(
            \App\Models\TaxModel::find($taxModelId),
            $this->year
        );
    }

    public function clearFilters(): void
    {
        $this->categories = [];
        $this->frequencies = [];
        $this->companyTypes = [];
        $this->tags = [];
        $this->proximity = null;
        $this->showOnlyIncomplete = false;
    }

    public function today(): void
    {
        $this->currentDate = Carbon::now();
        $this->year = (int) $this->currentDate->year;
    }

    public function nextPeriod(): void
    {
        match ($this->view) {
            'day' => $this->currentDate = $this->currentDate->addDay(),
            'week' => $this->currentDate = $this->currentDate->addWeek(),
            'month' => $this->currentDate = $this->currentDate->addMonth(),
            'list' => $this->currentDate = $this->currentDate->addYear(),
            'year' => $this->currentDate = $this->currentDate->addYear(),
            default => $this->currentDate = $this->currentDate->addYear(),
        };
        $this->year = (int) $this->currentDate->year;
    }

    public function previousPeriod(): void
    {
        match ($this->view) {
            'day' => $this->currentDate = $this->currentDate->subDay(),
            'week' => $this->currentDate = $this->currentDate->subWeek(),
            'month' => $this->currentDate = $this->currentDate->subMonth(),
            'list' => $this->currentDate = $this->currentDate->subYear(),
            'year' => $this->currentDate = $this->currentDate->subYear(),
            default => $this->currentDate = $this->currentDate->subYear(),
        };
        $this->year = (int) $this->currentDate->year;
    }

    public function getFilteredDeadlinesProperty(): Collection
    {
        $query = Deadline::query()
            ->with('taxModel')
            ->byYear($this->year);

        // Apply date range based on view mode
        [$startDate, $endDate] = match ($this->view) {
            'day' => [
                $this->currentDate->copy()->startOfDay(),
                $this->currentDate->copy()->endOfDay(),
            ],
            'week' => [
                $this->currentDate->copy()->startOfWeek(),
                $this->currentDate->copy()->endOfWeek(),
            ],
            'month' => [
                $this->currentDate->copy()->startOfMonth(),
                $this->currentDate->copy()->endOfMonth(),
            ],
            'list' => [
                $this->currentDate->copy()->startOfYear(),
                $this->currentDate->copy()->endOfYear(),
            ],
            'year' => [
                $this->currentDate->copy()->startOfYear(),
                $this->currentDate->copy()->endOfYear(),
            ],
            default => [
                $this->currentDate->copy()->startOfYear(),
                $this->currentDate->copy()->endOfYear(),
            ],
        };

        $query->byDateRange($startDate, $endDate);

        // Apply proximity filter
        if ($this->proximity) {
            $days = match ($this->proximity) {
                'next_7_days' => 7,
                'next_30_days' => 30,
                'next_60_days' => 60,
                'next_90_days' => 90,
                default => null,
            };

            if ($days) {
                $query->upcoming($days);
            }
        }

        $deadlines = $query->get();

        // Filter by tax model properties
        return $deadlines->filter(function (Deadline $deadline) {
            $taxModel = $deadline->taxModel;

            if (! $taxModel) {
                return false;
            }

            // Category filter
            if (! empty($this->categories) && ! in_array($taxModel->category, $this->categories)) {
                return false;
            }

            // Frequency filter
            if (! empty($this->frequencies) && ! in_array($taxModel->frequency, $this->frequencies)) {
                return false;
            }

            // Company type filter
            if (! empty($this->companyTypes)) {
                $applicable = is_array($taxModel->applicable_to) ? $taxModel->applicable_to : [];
                $hasMatch = ! empty(array_intersect($this->companyTypes, $applicable));
                if (! $hasMatch) {
                    return false;
                }
            }

            // Completion filter
            if ($this->showOnlyIncomplete && auth()->check()) {
                $isCompleted = auth()->user()->hasCompletedModel($taxModel, $this->year);
                if ($isCompleted) {
                    return false;
                }
            }

            return true;
        })->sortBy('deadline_date');
    }

    public function getAvailableCategoriesProperty(): array
    {
        return TaxModel::query()
            ->byYear($this->year)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }

    public function getAvailableFrequenciesProperty(): array
    {
        return TaxModel::query()
            ->byYear($this->year)
            ->distinct()
            ->pluck('frequency')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }

    public function getAvailableCompanyTypesProperty(): array
    {
        return ['autonomo', 'pyme', 'gran_empresa'];
    }

    public function canUseFilters(): bool
    {
        return true; // Basic filters are available to all users
    }

    public function canExport(): bool
    {
        return auth()->check();
    }

    public function canTrackCompletion(): bool
    {
        return auth()->check();
    }

    public function exportCsv(): BinaryFileResponse
    {
        if (! $this->canExport()) {
            abort(403, 'You must be logged in to export data.');
        }

        $timestamp = now()->format('Y-m-d_His');
        $filename = "calendario_fiscal_{$timestamp}.csv";

        return Excel::download(
            new DeadlinesExport($this->filteredDeadlines),
            $filename,
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function exportExcel(): BinaryFileResponse
    {
        if (! $this->canExport()) {
            abort(403, 'You must be logged in to export data.');
        }

        $timestamp = now()->format('Y-m-d_His');
        $filename = "calendario_fiscal_{$timestamp}.xlsx";

        return Excel::download(
            new DeadlinesExport($this->filteredDeadlines),
            $filename
        );
    }

    public function exportIcal(): StreamedResponse
    {
        if (! $this->canExport()) {
            abort(403, 'You must be logged in to export data.');
        }

        $calendar = Calendar::create('Calendario Fiscal '.$this->year)
            ->productIdentifier('Calendario Fiscal');

        foreach ($this->filteredDeadlines as $deadline) {
            $taxModel = $deadline->taxModel;

            if (! $taxModel) {
                continue;
            }

            $event = Event::create()
                ->name($taxModel->name.' - Modelo '.$taxModel->model_number)
                ->startsAt($deadline->deadline_date)
                ->endsAt($deadline->deadline_date)
                ->fullDay();

            if ($deadline->deadline_time) {
                $event->startsAt($deadline->deadline_date->setTimeFrom($deadline->deadline_time))
                    ->endsAt($deadline->deadline_date->setTimeFrom($deadline->deadline_time)->addHour())
                    ->fullDay(false);
            }

            if ($taxModel->description) {
                $event->description($taxModel->description);
            }

            if ($taxModel->aeat_url) {
                $event->url($taxModel->aeat_url);
            }

            $calendar->event($event);
        }

        $timestamp = now()->format('Y-m-d_His');
        $filename = "calendario_fiscal_{$timestamp}.ics";

        return response()->streamDownload(
            fn () => print $calendar->get(),
            $filename,
            ['Content-Type' => 'text/calendar; charset=utf-8']
        );
    }

    public function render()
    {
        return view('livewire.calendar.calendar-view', [
            'deadlines' => $this->filteredDeadlines,
            'availableCategories' => $this->availableCategories,
            'availableFrequencies' => $this->availableFrequencies,
            'availableCompanyTypes' => $this->availableCompanyTypes,
        ]);
    }
}
