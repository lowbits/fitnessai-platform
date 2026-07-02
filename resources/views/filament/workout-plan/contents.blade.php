@php
    /** @var \App\Models\WorkoutPlan $record */
    $record->loadMissing(['exercises.exercise']);
    $exercises = $record->exercises->sortBy('order')->values();

    $sectionLabel = [
        'warmup' => ['🔥', 'Warm-up', 'warning'],
        'main' => ['💪', 'Main work', 'primary'],
        'cooldown' => ['🧊', 'Cool-down', 'info'],
        'cardio' => ['🏃', 'Cardio', 'danger'],
    ];

    $grouped = $exercises->groupBy(fn ($wpe) => $wpe->type ?: 'main');
@endphp

<style>
    .fit-workout-board {
        --c-fg: var(--gray-950);
        --c-fg-strong: var(--gray-700);
        --c-fg-muted: var(--gray-500);
        --c-fg-faint: var(--gray-400);
        --c-surface: var(--gray-50);
        --c-border: var(--gray-200);
        --c-border-soft: var(--gray-100);
        --c-chip-bg: var(--gray-900);
        --c-chip-fg: #fff;
    }
    :where(.dark) .fit-workout-board {
        --c-fg: #fff;
        --c-fg-strong: var(--gray-200);
        --c-fg-muted: var(--gray-400);
        --c-fg-faint: var(--gray-500);
        --c-surface: color-mix(in oklab, #fff 5%, transparent);
        --c-border: color-mix(in oklab, #fff 10%, transparent);
        --c-border-soft: color-mix(in oklab, #fff 6%, transparent);
        --c-chip-bg: #fff;
        --c-chip-fg: var(--gray-950);
    }
</style>

@if ($exercises->isEmpty())
    <p style="color:var(--gray-500);font-style:italic;font-size:var(--text-sm);">No exercises in this workout.</p>
@else
    <div class="fit-workout-board" style="display:flex;flex-direction:column;gap:1.25rem;">
        @foreach ($grouped as $type => $items)
            @php
                [$icon, $label, $badgeColor] = $sectionLabel[$type] ?? ['🏋️', ucfirst((string) $type), 'gray'];
            @endphp

            <section>
                <header style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.625rem;">
                    <span style="font-size:1.125rem;line-height:1;">{{ $icon }}</span>
                    <h4 style="margin:0;font-size:var(--text-xs);font-weight:var(--font-weight-bold);text-transform:uppercase;letter-spacing:0.075em;color:var(--c-fg-strong);">{{ $label }}</h4>
                    <x-filament::badge :color="$badgeColor">{{ $items->count() }} {{ \Illuminate\Support\Str::plural('exercise', $items->count()) }}</x-filament::badge>
                </header>

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:0.75rem;">
                    @foreach ($items as $idx => $wpe)
                        @php
                            $ex = $wpe->exercise;
                            $img = $ex?->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($ex->image) : null;
                            $loadPrimary = match (true) {
                                $wpe->sets && $wpe->reps => $wpe->sets.' × '.$wpe->reps,
                                (bool) $wpe->duration_seconds => $wpe->duration_seconds.'s',
                                default => null,
                            };
                            $muscles = $ex?->primary_muscles ? array_slice((array) $ex->primary_muscles, 0, 3) : [];
                        @endphp

                        <x-filament::section :has-content-el="false">
                            <div style="display:flex;gap:0.875rem;padding:0.875rem;">
                                {{-- Order + image stack --}}
                                <div style="display:flex;flex-direction:column;align-items:center;gap:0.5rem;flex-shrink:0;">
                                    <span style="display:flex;align-items:center;justify-content:center;width:1.5rem;height:1.5rem;border-radius:9999px;background:var(--c-chip-bg);color:var(--c-chip-fg);font-size:var(--text-xs);font-weight:var(--font-weight-bold);">{{ $idx + 1 }}</span>
                                    <div style="width:4.5rem;height:4.5rem;border-radius:var(--radius-lg);overflow:hidden;background:var(--c-surface);display:flex;align-items:center;justify-content:center;">
                                        @if ($img)
                                            <img src="{{ $img }}" alt="{{ $ex?->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;" />
                                        @else
                                            <span style="font-size:1.75rem;opacity:0.3;">💪</span>
                                        @endif
                                    </div>
                                </div>

                                <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:0.5rem;">
                                    <div>
                                        <h5 style="margin:0;font-size:var(--text-sm);font-weight:var(--font-weight-bold);color:var(--c-fg);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                            {{ $ex?->name ?? '(missing exercise)' }}
                                        </h5>
                                        @if ($ex && ($ex->movement_pattern || $muscles))
                                            <div style="margin-top:0.125rem;font-size:var(--text-xs);color:var(--c-fg-muted);text-transform:capitalize;">
                                                @if ($ex->movement_pattern) {{ $ex->movement_pattern }} @endif
                                                @if ($ex->movement_pattern && $muscles) · @endif
                                                @if ($muscles) {{ implode(', ', $muscles) }} @endif
                                            </div>
                                        @endif
                                    </div>

                                    <div style="display:flex;flex-wrap:wrap;gap:0.875rem;align-items:flex-end;">
                                        @if ($loadPrimary)
                                            <div>
                                                <div style="font-family:var(--font-mono);font-size:var(--text-lg);font-weight:var(--font-weight-bold);line-height:1;color:var(--c-fg);">{{ $loadPrimary }}</div>
                                                <div style="margin-top:0.1875rem;font-size:var(--text-xs);font-weight:var(--font-weight-semibold);text-transform:uppercase;letter-spacing:0.05em;color:var(--c-fg-muted);">
                                                    {{ $wpe->sets && $wpe->reps ? 'sets × reps' : 'duration' }}
                                                </div>
                                            </div>
                                        @endif

                                        @if ($wpe->rest_seconds)
                                            <div>
                                                <div style="font-family:var(--font-mono);font-size:var(--text-sm);font-weight:var(--font-weight-semibold);line-height:1;color:var(--c-fg-strong);">{{ $wpe->rest_seconds }}s</div>
                                                <div style="margin-top:0.1875rem;font-size:var(--text-xs);font-weight:var(--font-weight-semibold);text-transform:uppercase;letter-spacing:0.05em;color:var(--c-fg-muted);">rest</div>
                                            </div>
                                        @endif

                                        @if ($wpe->rpe)
                                            <div>
                                                <div style="font-family:var(--font-mono);font-size:var(--text-sm);font-weight:var(--font-weight-semibold);line-height:1;color:var(--c-fg-strong);">RPE {{ $wpe->rpe }}</div>
                                                <div style="margin-top:0.1875rem;font-size:var(--text-xs);font-weight:var(--font-weight-semibold);text-transform:uppercase;letter-spacing:0.05em;color:var(--c-fg-muted);">intensity</div>
                                            </div>
                                        @endif

                                        @if ($wpe->weight_recommendation)
                                            <div>
                                                <div style="font-size:var(--text-sm);font-weight:var(--font-weight-semibold);line-height:1;color:var(--c-fg-strong);">{{ $wpe->weight_recommendation }}</div>
                                                <div style="margin-top:0.1875rem;font-size:var(--text-xs);font-weight:var(--font-weight-semibold);text-transform:uppercase;letter-spacing:0.05em;color:var(--c-fg-muted);">load</div>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($wpe->execution_style || $wpe->tempo)
                                        <div style="display:flex;flex-wrap:wrap;gap:0.375rem;">
                                            @if ($wpe->execution_style)
                                                <x-filament::badge color="info">{{ $wpe->execution_style }}</x-filament::badge>
                                            @endif
                                            @if ($wpe->tempo)
                                                <x-filament::badge color="primary">Tempo {{ $wpe->tempo }}</x-filament::badge>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </x-filament::section>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
@endif