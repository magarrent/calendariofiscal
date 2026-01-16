<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Calendar Subscription') }}</flux:heading>

    <x-settings.layout :heading="__('Suscripción al Calendario')" :subheading="__('Sincroniza tus plazos fiscales con tu calendario favorito')">

        <flux:separator variant="subtle" text="URL de Suscripción" class="my-6" />

        <div class="my-6 space-y-4">
            <flux:text>
                La suscripción al calendario te permite sincronizar automáticamente tus plazos fiscales con aplicaciones como Google Calendar, Outlook o Apple Calendar. El calendario se actualiza automáticamente cuando cambian tus modelos favoritos o perfil de empresa.
            </flux:text>

            @if ($subscriptionUrl)
                <div>
                    <flux:field>
                        <flux:label>Tu URL de Suscripción</flux:label>
                        <flux:description>Copia esta URL y agrégala a tu aplicación de calendario favorita</flux:description>

                        <div class="mt-3 flex gap-2">
                            <div class="flex-1">
                                <flux:input
                                    value="{{ $subscriptionUrl }}"
                                    readonly
                                    class="font-mono text-sm"
                                    id="subscription-url-input"
                                />
                            </div>
                            <flux:button
                                variant="filled"
                                icon="clipboard"
                                x-data="{ copied: @entangle('showCopiedMessage') }"
                                x-on:click="
                                    navigator.clipboard.writeText('{{ $subscriptionUrl }}');
                                    copied = true;
                                    setTimeout(() => copied = false, 2000);
                                    $wire.copyUrl();
                                "
                                x-text="copied ? 'Copiado' : 'Copiar'"
                            ></flux:button>
                        </div>
                    </flux:field>

                    @if ($showCopiedMessage)
                        <flux:text class="mt-2 !text-green-600 dark:!text-green-400">
                            URL copiada al portapapeles
                        </flux:text>
                    @endif
                </div>

                <flux:separator variant="subtle" text="Instrucciones" class="my-6" />

                <div class="space-y-4">
                    <div>
                        <flux:heading size="lg">Google Calendar</flux:heading>
                        <flux:text class="mt-2">
                            1. Abre Google Calendar<br>
                            2. Haz clic en el icono "+" junto a "Otros calendarios"<br>
                            3. Selecciona "Desde URL"<br>
                            4. Pega tu URL de suscripción<br>
                            5. Haz clic en "Agregar calendario"
                        </flux:text>
                    </div>

                    <div>
                        <flux:heading size="lg">Apple Calendar</flux:heading>
                        <flux:text class="mt-2">
                            1. Abre Calendar<br>
                            2. Ve a Archivo → Nueva suscripción a calendario<br>
                            3. Pega tu URL de suscripción<br>
                            4. Haz clic en "Suscribirse"<br>
                            5. Configura las opciones de actualización (recomendado: cada hora)
                        </flux:text>
                    </div>

                    <div>
                        <flux:heading size="lg">Microsoft Outlook</flux:heading>
                        <flux:text class="mt-2">
                            1. Abre Outlook<br>
                            2. Ve a Calendario<br>
                            3. Haz clic en "Agregar calendario" → "Desde Internet"<br>
                            4. Pega tu URL de suscripción<br>
                            5. Haz clic en "Aceptar"
                        </flux:text>
                    </div>
                </div>

                <flux:separator variant="subtle" text="Seguridad" class="my-6" />

                <div class="my-6">
                    <flux:heading size="lg">Regenerar URL</flux:heading>
                    <flux:text class="mt-2">
                        Si crees que tu URL de suscripción ha sido comprometida, puedes regenerarla. Esto invalidará la URL anterior y necesitarás actualizar la suscripción en todos tus calendarios.
                    </flux:text>
                    <div class="mt-4">
                        <flux:button
                            wire:click="regenerateToken"
                            wire:confirm="¿Estás seguro? Esto invalidará la URL anterior y tendrás que actualizar la suscripción en todos tus calendarios."
                            variant="danger"
                            icon="arrow-path"
                        >
                            Regenerar URL
                        </flux:button>
                    </div>

                    <x-action-message class="mt-3" on="subscription-token-regenerated">
                        URL regenerada correctamente.
                    </x-action-message>
                </div>

            @else
                <div class="my-6">
                    <flux:text class="mb-4">
                        Aún no tienes una URL de suscripción. Genera una para empezar a sincronizar tus plazos fiscales con tu calendario.
                    </flux:text>
                    <flux:button wire:click="generateToken" variant="primary" icon="plus">
                        Generar URL de Suscripción
                    </flux:button>

                    <x-action-message class="mt-3" on="subscription-token-generated">
                        URL generada correctamente.
                    </x-action-message>
                </div>
            @endif

        </div>

    </x-settings.layout>
</section>
