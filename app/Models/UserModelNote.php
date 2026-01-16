<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserModelNote extends Model
{
    /** @use HasFactory<\Database\Factories\UserModelNoteFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tax_model_id',
        'note',
        'filing_number',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taxModel(): BelongsTo
    {
        return $this->belongsTo(TaxModel::class);
    }
}
