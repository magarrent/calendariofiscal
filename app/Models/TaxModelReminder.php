<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxModelReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tax_model_id',
        'days_before',
        'enabled',
        'notification_type',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'days_before' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taxModel(): BelongsTo
    {
        return $this->belongsTo(TaxModel::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }
}
