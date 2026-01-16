@props([
    'disabled' => false,
    'tooltip' => 'Regístrate para desbloquear esta función',
])

<div class="relative inline-block">
    <div {{ $attributes->class(['opacity-50' => $disabled]) }}>
        {{ $slot }}
    </div>

    @if($disabled)
        <flux:tooltip :content="$tooltip" position="top" toggleable>
            <div class="absolute inset-0 cursor-not-allowed" wire:click.prevent></div>
        </flux:tooltip>
        <div class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2">
            <flux:icon.lock-closed class="size-4 text-gray-400" />
        </div>
    @endif
</div>
