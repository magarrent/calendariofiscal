@if($model)
    <flux:modal name="model-detail" variant="flyout" position="right" class="md:w-[600px]">
        <div class="flex h-full flex-col">
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:badge class="{{ $this->getCategoryColor($model->category ?? 'otros') }}">
                                Modelo {{ $model->model_number }}
                            </flux:badge>
                            <flux:badge variant="outline">
                                {{ $this->getFrequencyLabel($model->frequency) }}
                            </flux:badge>
                        </div>
                        <flux:heading size="xl" class="mt-3">
                            {{ $model->name }}
                        </flux:heading>
                    </div>
                </div>
            </div>

            {{-- Scrollable content area --}}
            <div class="flex-1 space-y-6 overflow-y-auto pr-2">
                {{-- Description --}}
                @if($model->description)
                    <div>
                        <flux:text class="text-gray-700 dark:text-gray-300">
                            {{ $model->description }}
                        </flux:text>
                    </div>
                @endif

                {{-- Deadlines --}}
                @if($model->deadlines->isNotEmpty())
                    <div>
                        <flux:heading size="lg" class="mb-3">Plazos de presentación</flux:heading>
                        <div class="space-y-2">
                            @foreach($model->deadlines->sortBy('deadline_date') as $deadline)
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                                    <div>
                                        <flux:text class="font-semibold">
                                            {{ $deadline->deadline_date->translatedFormat('d F Y') }}
                                        </flux:text>
                                        @if($deadline->deadline_time)
                                            <flux:text size="sm" class="text-gray-500">
                                                Hora límite: {{ $deadline->deadline_time->format('H:i') }}
                                            </flux:text>
                                        @endif
                                    </div>
                                    @if($deadline->deadline_date->isPast())
                                        <flux:badge variant="danger" size="sm">Vencido</flux:badge>
                                    @elseif($deadline->deadline_date->diffInDays(now()) <= 7)
                                        <flux:badge variant="warning" size="sm">Próximo</flux:badge>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Completion Status --}}
                <div>
                    <flux:heading size="lg" class="mb-3">Estado de cumplimiento</flux:heading>
                    <livewire:calendar.model-completion :tax-model-id="$modelId" :year="$model->year" wire:key="completion-{{ $modelId }}-{{ $model->year }}" />
                </div>

                {{-- Notification Reminders --}}
                @auth
                    <div>
                        <livewire:notifications.manage-reminders :tax-model-id="$modelId" wire:key="reminders-{{ $modelId }}" />
                    </div>
                @endauth

                {{-- Who must file --}}
                @if($model->applicable_to && is_array($model->applicable_to) && count($model->applicable_to) > 0)
                    <div>
                        <flux:heading size="lg" class="mb-3">Quién debe presentarlo</flux:heading>
                        <div class="flex flex-wrap gap-2">
                            @foreach($model->applicable_to as $type)
                                <flux:badge variant="outline">
                                    {{ match($type) {
                                        'autonomo' => 'Autónomos',
                                        'pyme' => 'PYME',
                                        'large_corp' => 'Grandes empresas',
                                        default => $type
                                    } }}
                                </flux:badge>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Toggle Detailed View --}}
                <div>
                    <flux:button wire:click="toggleDetails" variant="outline" class="w-full">
                        {{ $showDetails ? 'Ocultar detalles' : 'Ver más detalles' }}
                        <flux:icon.chevron-down class="ml-2 size-4 transition-transform {{ $showDetails ? 'rotate-180' : '' }}" />
                    </flux:button>
                </div>

                {{-- Detailed Information --}}
                @if($showDetails)
                    <div class="space-y-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                        {{-- Filing Instructions --}}
                        @if($model->instructions)
                            <div>
                                <flux:heading size="lg" class="mb-3">Instrucciones de presentación</flux:heading>
                                <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                    <flux:text class="whitespace-pre-line text-gray-700 dark:text-gray-300">
                                        {{ $model->instructions }}
                                    </flux:text>
                                </div>
                            </div>
                        @endif

                        {{-- Penalties --}}
                        @if($model->penalties)
                            <div>
                                <flux:heading size="lg" class="mb-3">Sanciones por incumplimiento</flux:heading>
                                <div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                                    <flux:text class="whitespace-pre-line text-red-900 dark:text-red-200">
                                        {{ $model->penalties }}
                                    </flux:text>
                                </div>
                            </div>
                        @endif

                        {{-- AEAT Link --}}
                        @if($model->aeat_url)
                            <div>
                                <flux:heading size="lg" class="mb-3">Enlaces oficiales</flux:heading>
                                <a
                                    href="{{ $model->aeat_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    <flux:icon.arrow-top-right-on-square class="size-4" />
                                    Ver en el sitio web de la AEAT
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </flux:modal>
@endif
