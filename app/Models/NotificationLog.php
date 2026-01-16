<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tax_model_id',
        'tax_model_reminder_id',
        'notification_type',
        'sent_at',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'details' => 'array',
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

    public function reminder(): BelongsTo
    {
        return $this->belongsTo(TaxModelReminder::class, 'tax_model_reminder_id');
    }
}
