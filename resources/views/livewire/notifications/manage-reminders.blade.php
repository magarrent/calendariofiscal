<div>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <flux:heading size="sm">Recordatorios</flux:heading>
            <flux:button wire:click="toggleAll" variant="ghost" size="sm">
                {{ $allEnabled ? 'Desactivar todos' : 'Activar todos' }}
            </flux:button>
        </div>

        @if(session()->has('message'))
            <flux:callout variant="success" class="mb-4">
                {{ session('message') }}
            </flux:callout>
        @endif

        <div class="space-y-3">
            @foreach($reminders as $days => $reminder)
                <div class="flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <flux:checkbox
                            wire:click="toggleReminder({{ $days }})"
                            :checked="$reminder['enabled']"
                        />
                        <flux:text>
                            {{ $days }} {{ $days === 1 ? 'día' : 'días' }} antes
                        </flux:text>
                    </div>
                    @if($reminder['enabled'])
                        <flux:badge variant="primary" size="sm">Activo</flux:badge>
                    @endif
                </div>
            @endforeach
        </div>

        @if($taxModelId)
            <flux:separator />

            <div>
                <flux:heading size="sm" class="mb-2">Vista previa de notificación</flux:heading>
                <flux:button wire:click="$dispatch('preview-notification', { taxModelId: {{ $taxModelId }} })" variant="outline" size="sm" class="w-full">
                    <flux:icon name="envelope" class="mr-2" variant="micro" />
                    Enviar notificación de prueba
                </flux:button>
            </div>
        @endif
    </div>
</div>
