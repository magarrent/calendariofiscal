<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_number',
        'name',
        'description',
        'group_description',
        'instructions',
        'penalties',
        'frequency',
        'applicable_to',
        'aeat_url',
        'category',
        'year',
        'source_document',
    ];

    protected function casts(): array
    {
        return [
            'applicable_to' => 'array',
            'year' => 'integer',
        ];
    }

    public function deadlines(): HasMany
    {
        return $this->hasMany(Deadline::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorites')
            ->withTimestamps();
    }

    public function scopeByCategory($query, ?string $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }

        return $query;
    }

    public function scopeByFrequency($query, ?string $frequency)
    {
        if ($frequency) {
            return $query->where('frequency', $frequency);
        }

        return $query;
    }

    public function scopeByYear($query, ?int $year = null)
    {
        return $query->where('year', $year ?? now()->year);
    }

    public function scopeApplicableTo($query, ?string $companyType)
    {
        if ($companyType) {
            return $query->whereJsonContains('applicable_to', $companyType);
        }

        return $query;
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(TaxModelReminder::class);
    }
}
