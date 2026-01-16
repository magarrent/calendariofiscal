<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_type',
        'subscription_token',
        'terms_accepted',
        'privacy_accepted',
        'terms_accepted_at',
        'privacy_accepted_at',
        'notification_frequency',
        'notification_types',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'terms_accepted' => 'boolean',
            'privacy_accepted' => 'boolean',
            'terms_accepted_at' => 'datetime',
            'privacy_accepted_at' => 'datetime',
            'notification_types' => 'array',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function favoriteTaxModels(): BelongsToMany
    {
        return $this->belongsToMany(TaxModel::class, 'user_favorites')
            ->withTimestamps();
    }

    public function hasFavorite(TaxModel $taxModel): bool
    {
        return $this->favoriteTaxModels()->where('tax_model_id', $taxModel->id)->exists();
    }

    public function toggleFavorite(TaxModel $taxModel): void
    {
        if ($this->hasFavorite($taxModel)) {
            $this->favoriteTaxModels()->detach($taxModel);
        } else {
            $this->favoriteTaxModels()->attach($taxModel);
        }
    }

    public function modelNotes(): BelongsToMany
    {
        return $this->belongsToMany(TaxModel::class, 'user_model_notes')
            ->withPivot(['note', 'filing_number'])
            ->withTimestamps();
    }

    public function deadlines(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserDeadline::class);
    }

    public function modelCompletions(): BelongsToMany
    {
        return $this->belongsToMany(TaxModel::class, 'user_model_completion')
            ->withPivot(['year', 'completed', 'completed_at'])
            ->withTimestamps();
    }

    public function hasCompletedModel(TaxModel $taxModel, int $year): bool
    {
        return $this->modelCompletions()
            ->where('tax_model_id', $taxModel->id)
            ->wherePivot('year', $year)
            ->wherePivot('completed', true)
            ->exists();
    }

    public function toggleModelCompletion(TaxModel $taxModel, int $year): void
    {
        $completion = UserModelCompletion::firstOrCreate([
            'user_id' => $this->id,
            'tax_model_id' => $taxModel->id,
            'year' => $year,
        ]);

        $completion->completed = ! $completion->completed;
        $completion->completed_at = $completion->completed ? now() : null;
        $completion->save();
    }

    public function taxModelReminders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TaxModelReminder::class);
    }

    public function notificationLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    /**
     * Generate a unique subscription token for the user.
     */
    public function generateSubscriptionToken(): string
    {
        do {
            $token = Str::random(64);
        } while (static::where('subscription_token', $token)->exists());

        $this->subscription_token = $token;
        $this->save();

        return $token;
    }

    /**
     * Regenerate the subscription token.
     */
    public function regenerateSubscriptionToken(): string
    {
        return $this->generateSubscriptionToken();
    }

    /**
     * Get the calendar subscription URL.
     */
    public function subscriptionUrl(): ?string
    {
        if (! $this->subscription_token) {
            return null;
        }

        return route('calendar.subscription.feed', ['token' => $this->subscription_token]);
    }
}
