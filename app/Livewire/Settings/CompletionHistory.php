<?php

namespace App\Livewire\Settings;

use App\Models\UserModelCompletion;
use Illuminate\Support\Collection;
use Livewire\Component;

class CompletionHistory extends Component
{
    public int $year;

    public function mount(): void
    {
        $this->year = (int) now()->year;
    }

    public function getCompletionsProperty(): Collection
    {
        if (! auth()->check()) {
            return collect();
        }

        return UserModelCompletion::with('taxModel')
            ->where('user_id', auth()->id())
            ->where('year', $this->year)
            ->where('completed', true)
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    public function undoCompletion(int $completionId): void
    {
        $completion = UserModelCompletion::where('id', $completionId)
            ->where('user_id', auth()->id())
            ->first();

        if ($completion) {
            $completion->completed = false;
            $completion->completed_at = null;
            $completion->save();

            $this->dispatch('completion-toggled');
        }
    }

    public function render()
    {
        return view('livewire.settings.completion-history');
    }
}
