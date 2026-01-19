<flux:modal name="model-detail" variant="flyout" position="right" class="md:w-[680px]">
    @if($model)
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap');

            .modal-detail-content {
                --official-blue: #0a3d62;
                --official-red: #c0392b;
                --gold-seal: #d4af37;
                --paper-bg: #fdfaf6;
                --ink-black: #1a1a1a;
                font-family: 'DM Sans', -apple-system, sans-serif;
                background: var(--paper-bg);
            }

            .dark .modal-detail-content {
                --paper-bg: #1a1a1a;
                --ink-black: #f5f5f5;
                --official-blue: #4a90e2;
                --official-red: #e74c3c;
            }

            .official-header {
                font-family: 'Cormorant Garamond', serif;
                font-weight: 700;
                letter-spacing: -0.02em;
                line-height: 1.1;
            }

            .modelo-stamp {
                display: inline-block;
                border: 3px solid var(--official-red);
                transform: rotate(-2deg);
                padding: 8px 16px;
                font-family: 'Cormorant Garamond', serif;
                font-weight: 700;
                font-size: 1.25rem;
                color: var(--official-red);
                background: rgba(192, 57, 43, 0.05);
                box-shadow: 0 4px 12px rgba(192, 57, 43, 0.15);
                animation: stamp-in 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            }

            @keyframes stamp-in {
                0% { transform: scale(0) rotate(-2deg); opacity: 0; }
                50% { transform: scale(1.1) rotate(-2deg); }
                100% { transform: scale(1) rotate(-2deg); opacity: 1; }
            }

            .frequency-badge {
                background: linear-gradient(135deg, var(--official-blue), #1e5f8f);
                color: white;
                padding: 4px 12px;
                border-radius: 4px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                box-shadow: 0 2px 8px rgba(10, 61, 98, 0.3);
            }

            .deadline-card {
                position: relative;
                border-left: 4px solid var(--official-blue);
                background: white;
                padding: 20px;
                margin-bottom: 16px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
                animation: slide-in 0.5s ease-out backwards;
            }

            .dark .deadline-card {
                background: #242424;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
            }

            .deadline-card:hover {
                transform: translateX(4px);
                box-shadow: 0 4px 16px rgba(10, 61, 98, 0.15);
            }

            @keyframes slide-in {
                from { opacity: 0; transform: translateX(20px); }
                to { opacity: 1; transform: translateX(0); }
            }

            .deadline-card:nth-child(1) { animation-delay: 0.1s; }
            .deadline-card:nth-child(2) { animation-delay: 0.15s; }
            .deadline-card:nth-child(3) { animation-delay: 0.2s; }
            .deadline-card:nth-child(4) { animation-delay: 0.25s; }

            .date-range {
                font-size: 1.125rem;
                font-weight: 700;
                color: var(--ink-black);
                font-family: 'DM Sans', sans-serif;
            }

            .date-arrow {
                color: var(--official-blue);
                margin: 0 8px;
                font-weight: 400;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .status-expired {
                background: linear-gradient(135deg, #c0392b, #e74c3c);
                color: white;
                box-shadow: 0 2px 8px rgba(192, 57, 43, 0.3);
            }

            .status-upcoming {
                background: linear-gradient(135deg, #f39c12, #f1c40f);
                color: #1a1a1a;
                box-shadow: 0 2px 8px rgba(243, 156, 18, 0.3);
            }

            .seal-progress {
                position: relative;
                height: 8px;
                background: rgba(10, 61, 98, 0.1);
                border-radius: 4px;
                overflow: hidden;
            }

            .seal-progress-bar {
                height: 100%;
                background: linear-gradient(90deg, var(--official-blue), #1e5f8f);
                border-radius: 4px;
                position: relative;
                transition: width 0.8s cubic-bezier(0.65, 0, 0.35, 1);
                animation: progress-fill 1.2s ease-out;
            }

            @keyframes progress-fill {
                from { width: 0 !important; }
            }

            .seal-progress-bar.danger {
                background: linear-gradient(90deg, #c0392b, #e74c3c);
            }

            .seal-progress-bar.warning {
                background: linear-gradient(90deg, #f39c12, #f1c40f);
            }

            .seal-progress-bar.success {
                background: linear-gradient(90deg, #27ae60, #2ecc71);
            }

            .progress-label {
                display: flex;
                justify-content: space-between;
                font-size: 0.625rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-top: 6px;
                color: var(--official-blue);
            }

            .section-divider {
                height: 2px;
                background: linear-gradient(90deg, var(--official-blue), transparent);
                margin: 32px 0;
                position: relative;
            }

            .section-divider::after {
                content: '●';
                position: absolute;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                color: var(--official-blue);
                font-size: 8px;
            }

            .section-heading {
                font-family: 'Cormorant Garamond', serif;
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--official-blue);
                margin-bottom: 16px;
                letter-spacing: -0.01em;
            }

            .aeat-link {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 20px;
                background: linear-gradient(135deg, var(--official-blue), #1e5f8f);
                color: white;
                text-decoration: none;
                border-radius: 6px;
                font-weight: 600;
                font-size: 0.875rem;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(10, 61, 98, 0.3);
            }

            .aeat-link:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(10, 61, 98, 0.4);
                background: linear-gradient(135deg, #1e5f8f, var(--official-blue));
            }

            .document-number {
                font-family: 'Courier New', monospace;
                font-size: 0.75rem;
                color: #666;
                letter-spacing: 0.1em;
            }

            .dark .document-number {
                color: #999;
            }

            .completion-toggle {
                display: inline-flex;
                align-items: center;
                gap: 12px;
                padding: 16px;
                background: white;
                border: 2px solid var(--official-blue);
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .dark .completion-toggle {
                background: #242424;
            }

            .completion-toggle:hover {
                background: rgba(10, 61, 98, 0.05);
                transform: scale(1.02);
            }

            .fade-in {
                animation: fade-in 0.6s ease-out;
            }

            @keyframes fade-in {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        </style>

        <div class="modal-detail-content flex h-full flex-col p-8">
            {{-- Official Header --}}
            <div class="mb-8 fade-in">
                <div class="mb-4 flex items-start justify-between">
                    <div class="modelo-stamp">
                        MODELO {{ $model->model_number }}
                    </div>
                    <span class="frequency-badge">{{ $this->getFrequencyLabel($model->frequency) }}</span>
                </div>

                <h1 class="official-header text-4xl mb-3" style="color: var(--ink-black);">
                    {{ $model->name }}
                </h1>

                @if($model->category)
                    <div class="document-number">
                        DOC-{{ strtoupper($model->category) }}-{{ $model->year ?? '2026' }}
                    </div>
                @endif
            </div>

            {{-- Scrollable Content --}}
            <div class="flex-1 overflow-y-auto pr-2" style="scrollbar-width: thin;">
                {{-- Description --}}
                @if($model->description)
                    <div class="mb-8 fade-in" style="animation-delay: 0.2s;">
                        <p style="color: var(--ink-black); line-height: 1.7; font-size: 0.938rem;">
                            {{ $model->description }}
                        </p>
                    </div>
                @endif

                {{-- Deadlines Section --}}
                @if($model->deadlines->isNotEmpty())
                    <div class="section-divider"></div>

                    <h2 class="section-heading">Plazos de Presentación</h2>

                    <div class="space-y-4">
                        @foreach($model->deadlines->sortBy('deadline_date') as $deadline)
                            <div class="deadline-card">
                                {{-- Period --}}
                                @if($deadline->period)
                                    <div class="mb-3">
                                        <span class="frequency-badge" style="font-size: 0.875rem;">
                                            {{ $deadline->period }}
                                        </span>
                                        @if($deadline->deadline_scope)
                                            <span class="frequency-badge" style="background: linear-gradient(135deg, #f39c12, #f1c40f); color: #1a1a1a; font-size: 0.875rem; margin-left: 8px;">
                                                {{ $deadline->deadline_scope }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Period Description --}}
                                @if($deadline->period_description)
                                    <div class="mb-2 text-sm font-medium" style="color: #666;">
                                        {{ $deadline->period_description }}
                                    </div>
                                @endif

                                {{-- Date Range --}}
                                <div class="mb-3 flex items-center justify-between">
                                    <div class="date-range">
                                        @if($deadline->period_start && $deadline->period_end)
                                            {{ $deadline->period_start->translatedFormat('d M Y') }}
                                            <span class="date-arrow">→</span>
                                            {{ $deadline->period_end->translatedFormat('d M Y') }}
                                            <div class="text-xs font-normal mt-1" style="color: #666;">
                                                {{ $deadline->days_to_complete }} {{ $deadline->days_to_complete === 1 ? 'día' : 'días' }} para completar
                                            </div>
                                        @else
                                            {{ $deadline->deadline_date->translatedFormat('d F Y') }}
                                        @endif

                                        @if($deadline->deadline_time)
                                            <div class="text-xs font-normal mt-1" style="color: #666;">
                                                Hora límite: {{ $deadline->deadline_time->format('H:i') }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Status Badge --}}
                                    @if($deadline->deadline_date->isPast())
                                        <span class="status-badge status-expired">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                                                <circle cx="6" cy="6" r="6"/>
                                            </svg>
                                            Vencido
                                        </span>
                                    @elseif($deadline->deadline_date->diffInDays(now()) <= 7)
                                        <span class="status-badge status-upcoming">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                                                <circle cx="6" cy="6" r="6"/>
                                            </svg>
                                            Próximo
                                        </span>
                                    @endif
                                </div>

                                {{-- Progress Bar --}}
                                @if($deadline->period_start && $deadline->period_end && $deadline->days_to_complete > 0)
                                    @php
                                        $now = now();
                                        $totalDays = $deadline->days_to_complete;
                                        $elapsedDays = max(0, min($totalDays, $deadline->period_start->diffInDays($now)));
                                        $progressPercent = $totalDays > 0 ? ($elapsedDays / $totalDays) * 100 : 0;
                                        $progressPercent = min(100, max(0, $progressPercent));

                                        $progressClass = match(true) {
                                            $progressPercent >= 90 => 'danger',
                                            $progressPercent >= 70 => 'warning',
                                            default => 'success'
                                        };
                                    @endphp

                                    <div class="seal-progress">
                                        <div class="seal-progress-bar {{ $progressClass }}" style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                    <div class="progress-label">
                                        <span>Inicio</span>
                                        <span>{{ number_format($progressPercent, 0) }}%</span>
                                        <span>Fin</span>
                                    </div>
                                @endif

                                {{-- Details --}}
                                @if($deadline->details)
                                    <div class="mt-3 rounded-lg p-3" style="background: rgba(10, 61, 98, 0.05); border-left: 3px solid var(--official-blue);">
                                        <div class="text-sm" style="color: #666; line-height: 1.6;">{{ $deadline->details }}</div>
                                    </div>
                                @endif

                                {{-- Conditions --}}
                                @if($deadline->conditions)
                                    <div class="mt-3 rounded-lg p-3" style="background: rgba(243, 156, 18, 0.1); border-left: 3px solid #f39c12;">
                                        <div class="flex items-start gap-2">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#f39c12" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                                                <circle cx="8" cy="8" r="7"/>
                                                <path d="M8 4v4M8 10v1"/>
                                            </svg>
                                            <div>
                                                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #f39c12; margin-bottom: 4px;">Condiciones de aplicación</div>
                                                <div class="text-sm" style="color: #666; line-height: 1.5;">{{ $deadline->conditions }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Notes --}}
                                @if($deadline->notes)
                                    <div class="mt-3 text-sm" style="color: #666; font-style: italic;">
                                        {{ $deadline->notes }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Completion Status --}}
                <div class="section-divider"></div>
                <h2 class="section-heading">Estado de Cumplimiento</h2>
                <p class="text-sm mb-4" style="color: #666; line-height: 1.6;">
                    Utiliza el interruptor para marcar si has completado este modelo fiscal o si aún está pendiente. Esto te ayudará a llevar un seguimiento de tus obligaciones fiscales.
                </p>
                <div class="mb-6">
                    <livewire:calendar.model-completion :tax-model-id="$modelId" :year="$model->year" wire:key="completion-{{ $modelId }}-{{ $model->year }}" />
                </div>

                {{-- Notification Reminders --}}
                <div class="mb-6 {{ !auth()->check() ? 'opacity-60 pointer-events-none' : '' }}">
                    @guest
                        <div class="rounded-lg p-4 mb-3" style="background: rgba(10, 61, 98, 0.05); border-left: 4px solid var(--official-blue);">
                            <div class="flex items-start gap-2">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="var(--official-blue)" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                                    <circle cx="8" cy="8" r="7"/>
                                    <path d="M8 4v4M8 10v1"/>
                                </svg>
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--official-blue);">Función Premium</div>
                                    <div class="text-sm" style="color: #666; line-height: 1.5;">
                                        <a href="/login" class="underline hover:text-blue-600">Inicia sesión</a> o <a href="/register" class="underline hover:text-blue-600">regístrate</a> para activar recordatorios automáticos por correo electrónico.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endguest
                    <livewire:notifications.manage-reminders :tax-model-id="$modelId" wire:key="reminders-{{ $modelId }}" />
                </div>

                {{-- Who Must File --}}
                @if($model->applicable_to && is_array($model->applicable_to) && count($model->applicable_to) > 0)
                    <div class="section-divider"></div>
                    <h2 class="section-heading">Quién Debe Presentarlo</h2>
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($model->applicable_to as $type)
                            <span class="frequency-badge">
                                {{ match($type) {
                                    'autonomo' => 'Autónomos',
                                    'pyme' => 'PYME',
                                    'large_corp' => 'Grandes Empresas',
                                    default => $type
                                } }}
                            </span>
                        @endforeach
                    </div>
                @endif

                {{-- Toggle Details --}}
                <div class="mb-6">
                    <button wire:click="toggleDetails" class="completion-toggle w-full justify-between">
                        <span style="color: var(--official-blue); font-weight: 600;">
                            {{ $showDetails ? 'Ocultar Información Adicional' : 'Ver Información Adicional' }}
                        </span>
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 20 20"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            style="color: var(--official-blue); transform: {{ $showDetails ? 'rotate(180deg)' : 'rotate(0)' }}; transition: transform 0.3s ease;"
                        >
                            <path d="M5 7.5L10 12.5L15 7.5"/>
                        </svg>
                    </button>
                </div>

                {{-- Detailed Information --}}
                @if($showDetails)
                    <div class="space-y-6 fade-in">
                        {{-- Instructions --}}
                        @if($model->instructions)
                            <div>
                                <h3 class="section-heading text-xl">Instrucciones de Presentación</h3>
                                <div class="rounded-lg p-6" style="background: rgba(10, 61, 98, 0.05); border-left: 4px solid var(--official-blue);">
                                    <p style="color: var(--ink-black); line-height: 1.7; white-space: pre-line; font-size: 0.875rem;">{{ $model->instructions }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Penalties --}}
                        @if($model->penalties)
                            <div>
                                <h3 class="section-heading text-xl">Sanciones por Incumplimiento</h3>
                                <div class="rounded-lg p-6" style="background: rgba(192, 57, 43, 0.05); border-left: 4px solid var(--official-red);">
                                    <p style="color: var(--official-red); line-height: 1.7; white-space: pre-line; font-size: 0.875rem; font-weight: 500;">{{ $model->penalties }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- AEAT Link --}}
                        @if($model->aeat_url)
                            <div>
                                <h3 class="section-heading text-xl">Enlaces Oficiales</h3>
                                <a
                                    href="{{ $model->aeat_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="aeat-link"
                                >
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M10 3L17 10L10 17M17 10H3"/>
                                    </svg>
                                    Ver en el Sitio Web de la AEAT
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</flux:modal>
