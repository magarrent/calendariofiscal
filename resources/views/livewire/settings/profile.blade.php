<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile Settings') }}</flux:heading>

    <x-settings.layout :heading="__('Perfil')" :subheading="__('Gestiona tu información personal y preferencias')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:separator variant="subtle" text="Información Personal" />

            <flux:input wire:model="name" label="Nombre" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" label="Correo Electrónico" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            Tu correo electrónico no está verificado.

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                Haz clic aquí para reenviar el correo de verificación.
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <flux:separator variant="subtle" text="Perfil de Empresa" />

            <flux:select wire:model="company_type" label="Tipo de Empresa" placeholder="Selecciona el tipo de empresa">
                <option value="">Sin especificar</option>
                <option value="autonomo">Autónomo</option>
                <option value="pyme">PYME</option>
                <option value="large_corp">Gran Empresa</option>
            </flux:select>

            <flux:separator variant="subtle" text="Preferencias de Notificaciones" />

            <flux:select wire:model="notification_frequency" label="Frecuencia de Notificaciones" required>
                <option value="daily">Diario</option>
                <option value="weekly">Semanal</option>
                <option value="monthly">Mensual</option>
                <option value="never">Nunca</option>
            </flux:select>

            <div>
                <flux:field>
                    <flux:label>Tipos de Notificaciones</flux:label>
                    <flux:description>Selecciona qué tipo de notificaciones deseas recibir</flux:description>

                    <div class="mt-3 space-y-3">
                        <flux:checkbox wire:model="notification_types" value="deadline_reminder" label="Recordatorios de plazos" />
                        <flux:checkbox wire:model="notification_types" value="new_model" label="Nuevos modelos disponibles" />
                        <flux:checkbox wire:model="notification_types" value="model_update" label="Actualizaciones de modelos" />
                        <flux:checkbox wire:model="notification_types" value="summary" label="Resumen periódico" />
                    </div>
                </flux:field>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">Guardar Cambios</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    Guardado.
                </x-action-message>
            </div>
        </form>

        <flux:separator variant="subtle" text="Exportar Datos" class="my-8" />

        <div class="my-6">
            <flux:heading size="lg">Exportar mis datos (GDPR)</flux:heading>
            <flux:text class="mt-2">
                Descarga una copia de todos tus datos personales almacenados en la plataforma, incluyendo favoritos, plazos personalizados, notas y estado de completitud de modelos.
            </flux:text>
            <div class="mt-4">
                <flux:button wire:click="exportUserData" variant="ghost" icon="arrow-down-tray">
                    Exportar Datos
                </flux:button>
            </div>
        </div>

        @if ($this->showDeleteUser)
            <livewire:settings.delete-user-form />
        @endif
    </x-settings.layout>
</section>
