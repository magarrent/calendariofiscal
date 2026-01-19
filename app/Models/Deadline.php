<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deadline extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_model_id',
        'uid',
        'deadline_date',
        'deadline_time',
        'period',
        'period_start',
        'period_end',
        'deadline_label',
        'check_day_1',
        'check_day_10',
        'rule_start_date',
        'is_variable',
        'page_number',
        'details',
        'deadline_scope',
        'conditions',
        'days_to_complete',
        'period_description',
        'year',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'deadline_date' => 'date',
            'deadline_time' => 'datetime:H:i',
            'period_start' => 'date',
            'period_end' => 'date',
            'check_day_1' => 'datetime',
            'check_day_10' => 'datetime',
            'rule_start_date' => 'date',
            'is_variable' => 'boolean',
            'page_number' => 'integer',
            'days_to_complete' => 'integer',
            'year' => 'integer',
        ];
    }

    public function taxModel(): BelongsTo
    {
        return $this->belongsTo(TaxModel::class);
    }

    public function scopeUpcoming($query, int $days = 30)
    {
        return $query->where('deadline_date', '>=', now())
            ->where('deadline_date', '<=', now()->addDays($days))
            ->orderBy('deadline_date');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('deadline_date', [$startDate, $endDate]);
    }

    public function scopeByYear($query, ?int $year = null)
    {
        return $query->where('year', $year ?? now()->year);
    }
}
