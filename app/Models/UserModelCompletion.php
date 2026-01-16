<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserModelCompletion extends Model
{
    /** @use HasFactory<\Database\Factories\UserModelCompletionFactory> */
    use HasFactory;

    protected $table = 'user_model_completion';

    protected $fillable = [
        'user_id',
        'tax_model_id',
        'year',
        'completed',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'completed' => 'boolean',
            'completed_at' => 'datetime',
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
}
