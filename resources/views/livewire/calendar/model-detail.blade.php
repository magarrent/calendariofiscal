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
                        <div class="space-y-3">
                            @foreach($model->deadlines->sortBy('deadline_date') as $deadline)
                                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                    {{-- Period Description --}}
                                    @if($deadline->period_description)
                                        <flux:text size="sm" class="mb-2 font-medium text-gray-600 dark:text-gray-400">
                                            {{ $deadline->period_description }}
                                        </flux:text>
                                    @endif

                                    {{-- Main Date Range Display --}}
                                    <div class="mb-3 flex items-center justify-between">
                                        <div class="flex-1">
                                            @if($deadline->period_start && $deadline->period_end)
                                                <flux:text class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $deadline->period_start->translatedFormat('d M Y') }} → {{ $deadline->period_end->translatedFormat('d M Y') }}
                                                </flux:text>
                                                <flux:text size="sm" class="text-gray-600 dark:text-gray-400">
                                                    ({{ $deadline->days_to_complete }} {{ $deadline->days_to_complete === 1 ? 'día' : 'días' }} para completar)
                                                </flux:text>
                                            @else
                                                <flux:text class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $deadline->deadline_date->translatedFormat('d F Y') }}
                                                </flux:text>
                                            @endif

                                            @if($deadline->deadline_time)
                                                <flux:text size="sm" class="text-gray-500">
                                                    Hora límite: {{ $deadline->deadline_time->format('H:i') }}
                                                </flux:text>
                                            @endif
                                        </div>

                                        {{-- Status Badge --}}
                                        @if($deadline->deadline_date->isPast())
                                            <flux:badge variant="danger" size="sm">Vencido</flux:badge>
                                        @elseif($deadline->deadline_date->diffInDays(now()) <= 7)
                                            <flux:badge variant="warning" size="sm">Próximo</flux:badge>
                                        @endif
                                    </div>

                                    {{-- Visual Timeline --}}
                                    @if($deadline->period_start && $deadline->period_end && $deadline->days_to_complete > 0)
                                        <div class="relative">
                                            {{-- Progress Bar Background --}}
                                            <div class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                                @php
                                                    $now = now();
                                                    $totalDays = $deadline->days_to_complete;
                                                    $elapsedDays = max(0, min($totalDays, $deadline->period_start->diffInDays($now)));
                                                    $progressPercent = $totalDays > 0 ? ($elapsedDays / $totalDays) * 100 : 0;
                                                    $progressPercent = min(100, max(0, $progressPercent));

                                                    // Determine color based on progress
                                                    $colorClass = match(true) {
                                                        $progressPercent >= 90 => 'bg-red-500',
                                                        $progressPercent >= 70 => 'bg-orange-500',
                                                        $progressPercent >= 50 => 'bg-yellow-500',
                                                        default => 'bg-green-500'
                                                    };
                                                @endphp

                                                {{-- Progress Bar Fill --}}
                                                <div class="h-full {{ $colorClass }} transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                                            </div>

                                            {{-- Timeline Labels --}}
                                            <div class="mt-1 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                                <span>Inicio</span>
                                                <span>{{ number_format($progressPercent, 0) }}%</span>
                                                <span>Fin</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Notes --}}
                                    @if($deadline->notes)
                                        <flux:text size="sm" class="mt-2 text-gray-600 dark:text-gray-400">
                                            {{ $deadline->notes }}
                                        </flux:text>
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
