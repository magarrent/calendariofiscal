<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">Historial de cumplimiento</flux:heading>

    <x-settings.layout heading="Historial de cumplimiento" subheading="Modelos marcados como completados en {{ $year }}">
        @if($this->completions->isEmpty())
            <div class="py-12 text-center">
                <flux:icon.document-check class="mx-auto size-12 text-gray-400" />
                <flux:heading size="lg" class="mt-4">No hay modelos completados</flux:heading>
                <flux:text class="mt-2 text-gray-500">
                    Cuando marques modelos como completados, aparecerán aquí.
                </flux:text>
            </div>
        @else
            <div class="space-y-3">
                @foreach($this->completions as $completion)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                        <div class="flex items-start gap-3">
                            <div class="mt-1">
                                <flux:icon.check-circle class="size-6 text-green-600 dark:text-green-400" />
                            </div>
                            <div>
                                <flux:heading size="sm">{{ $completion->taxModel->name }}</flux:heading>
                                <flux:text class="mt-1">
                                    <flux:badge variant="primary" size="sm">{{ ucfirst($completion->taxModel->category ?? 'otros') }}</flux:badge>
                                    <flux:badge size="sm" class="ml-2">Modelo {{ $completion->taxModel->model_number }}</flux:badge>
                                </flux:text>
                                <flux:text size="sm" class="mt-2 text-gray-500">
                                    Completado {{ $completion->completed_at->diffForHumans() }}
                                    <span class="mx-2">•</span>
                                    {{ $completion->completed_at->translatedFormat('d M Y, H:i') }}
                                </flux:text>
                            </div>
                        </div>
                        <div>
                            <flux:button
                                wire:click="undoCompletion({{ $completion->id }})"
                                variant="ghost"
                                size="sm"
                                icon="arrow-uturn-left"
                            >
                                Deshacer
                            </flux:button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 text-center">
                <flux:text size="sm" class="text-gray-500">
                    Mostrando {{ $this->completions->count() }} modelo(s) completado(s)
                </flux:text>
            </div>
        @endif
    </x-settings.layout>
</section>
