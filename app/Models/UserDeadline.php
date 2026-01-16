<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDeadline extends Model
{
    /** @use HasFactory<\Database\Factories\UserDeadlineFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'deadline_date',
        'deadline_time',
        'year',
    ];

    protected function casts(): array
    {
        return [
            'deadline_date' => 'date',
            'deadline_time' => 'datetime:H:i',
            'year' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
