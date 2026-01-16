<?php

use Livewire\Component;
use App\Models\TaxModel;
use App\Models\UserModelCompletion;
use Livewire\Attributes\On;

new class extends Component
{
    public int $taxModelId;
    public int $year;
    public bool $isCompleted = false;
    public ?string $completedAt = null;

    public function mount(int $taxModelId, int $year): void
    {
        $this->taxModelId = $taxModelId;
        $this->year = $year;
        $this->loadCompletionStatus();
    }

    public function loadCompletionStatus(): void
    {
        if (!auth()->check()) {
            return;
        }

        $completion = UserModelCompletion::where('user_id', auth()->id())
            ->where('tax_model_id', $this->taxModelId)
            ->where('year', $this->year)
            ->first();

        $this->isCompleted = $completion?->completed ?? false;
        $this->completedAt = $completion?->completed_at?->diffForHumans();
    }

    public function toggleCompletion(): void
    {
        if (!auth()->check()) {
            $this->dispatch('notify', message: 'Debe iniciar sesión para marcar modelos como completados');
            return;
        }

        auth()->user()->toggleModelCompletion(TaxModel::find($this->taxModelId), $this->year);
        $this->loadCompletionStatus();
        $this->dispatch('completion-toggled');
    }

    #[On('completion-toggled')]
    public function refresh(): void
    {
        $this->loadCompletionStatus();
    }
};
?>

<div class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-700">
    <div class="flex items-center gap-3">
        <flux:switch
            wire:click="toggleCompletion"
            :checked="$isCompleted"
            :disabled="!auth()->check()"
        />
        <div>
            <flux:text class="font-medium">
                {{ $isCompleted ? 'Completado' : 'Pendiente' }}
            </flux:text>
            @if($isCompleted && $completedAt)
                <flux:text size="sm" class="text-gray-500">
                    Marcado como completado {{ $completedAt }}
                </flux:text>
            @endif
        </div>
    </div>

    @if($isCompleted)
        <flux:badge variant="success">
            <flux:icon.check class="mr-1 size-3" />
            Completado
        </flux:badge>
    @else
        <flux:badge variant="warning">
            <flux:icon.clock class="mr-1 size-3" />
            Pendiente
        </flux:badge>
    @endif
</div>
