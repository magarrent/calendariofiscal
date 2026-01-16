<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CalendarSubscription extends Component
{
    public ?string $subscriptionUrl = null;

    public bool $showCopiedMessage = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->subscriptionUrl = $user->subscriptionUrl();
    }

    /**
     * Generate a new subscription token.
     */
    public function generateToken(): void
    {
        $user = Auth::user();
        $user->generateSubscriptionToken();
        $this->subscriptionUrl = $user->subscriptionUrl();

        $this->dispatch('subscription-token-generated');
    }

    /**
     * Regenerate the subscription token.
     */
    public function regenerateToken(): void
    {
        $user = Auth::user();
        $user->regenerateSubscriptionToken();
        $this->subscriptionUrl = $user->subscriptionUrl();

        $this->dispatch('subscription-token-regenerated');
    }

    /**
     * Copy the subscription URL to clipboard.
     */
    public function copyUrl(): void
    {
        $this->showCopiedMessage = true;
    }

    public function render()
    {
        return view('livewire.settings.calendar-subscription');
    }
}
