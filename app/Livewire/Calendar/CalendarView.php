<?php

namespace App\Livewire\Calendar;

use App\Models\Deadline;
use App\Models\TaxModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Session;
use Livewire\Component;

class CalendarView extends Component
{
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

    public Carbon $currentDate;

    public int $year = 2026;

    public function mount(): void
    {
        $this->currentDate = Carbon::now();
        $this->year = (int) now()->year;
    }

    public function clearFilters(): void
    {
        $this->categories = [];
        $this->frequencies = [];
        $this->companyTypes = [];
        $this->tags = [];
        $this->proximity = null;
    }

    public function today(): void
    {
        $this->currentDate = Carbon::now();
    }

    public function nextPeriod(): void
    {
        $this->currentDate = match ($this->view) {
            'day' => $this->currentDate->addDay(),
            'week' => $this->currentDate->addWeek(),
            'month' => $this->currentDate->addMonth(),
            'year' => $this->currentDate->addYear(),
            default => $this->currentDate->addMonth(),
        };
    }

    public function previousPeriod(): void
    {
        $this->currentDate = match ($this->view) {
            'day' => $this->currentDate->subDay(),
            'week' => $this->currentDate->subWeek(),
            'month' => $this->currentDate->subMonth(),
            'year' => $this->currentDate->subYear(),
            default => $this->currentDate->subMonth(),
        };
    }

    public function getFilteredDeadlinesProperty(): Collection
    {
        $query = Deadline::query()
            ->with('taxModel')
            ->byYear($this->year);

        // Apply date range based on view
        $startDate = match ($this->view) {
            'day' => $this->currentDate->copy()->startOfDay(),
            'week' => $this->currentDate->copy()->startOfWeek(),
            'month' => $this->currentDate->copy()->startOfMonth(),
            'year' => $this->currentDate->copy()->startOfYear(),
            'timeline', 'list' => now()->startOfYear(),
            default => $this->currentDate->copy()->startOfMonth(),
        };

        $endDate = match ($this->view) {
            'day' => $this->currentDate->copy()->endOfDay(),
            'week' => $this->currentDate->copy()->endOfWeek(),
            'month' => $this->currentDate->copy()->endOfMonth(),
            'year' => $this->currentDate->copy()->endOfYear(),
            'timeline', 'list' => now()->endOfYear(),
            default => $this->currentDate->copy()->endOfMonth(),
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
