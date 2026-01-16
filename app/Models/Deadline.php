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
        'deadline_date',
        'deadline_time',
        'year',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'deadline_date' => 'date',
            'deadline_time' => 'datetime:H:i',
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
