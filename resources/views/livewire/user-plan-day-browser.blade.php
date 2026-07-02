@php
    $plan = $this->plan;
    $mealPlan = $plan?->mealPlans->first();
    $workoutPlan = $plan?->workoutPlans->first();
    $totalDays = $plan?->duration_days ?? 0;
    $allPlans = $user->plans()->latest('created_at')->get(['id', 'status', 'duration_days', 'start_date', 'daily_calories']);
@endphp

<div class="fit-day-browser" style="display:flex;flex-direction:column;gap:1rem;">
    <style>
        .fit-day-browser {
            --c-fg: var(--gray-950);
            --c-fg-strong: var(--gray-700);
            --c-fg-muted: var(--gray-500);
            --c-surface: var(--gray-50);
            --c-border: var(--gray-200);
        }
        :where(.dark) .fit-day-browser {
            --c-fg: #fff;
            --c-fg-strong: var(--gray-200);
            --c-fg-muted: var(--gray-400);
            --c-surface: color-mix(in oklab, #fff 5%, transparent);
            --c-border: color-mix(in oklab, #fff 10%, transparent);
        }
    </style>

    @if ($allPlans->isEmpty())
        <p style="color:var(--c-fg-muted);font-style:italic;font-size:var(--text-sm);">This user has no plans yet.</p>
    @else
        @if ($allPlans->count() > 1)
            <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                <span style="font-size:var(--text-xs);font-weight:var(--font-weight-semibold);text-transform:uppercase;letter-spacing:0.05em;color:var(--c-fg-muted);">Plan:</span>
                @foreach ($allPlans as $p)
                    <button
                        type="button"
                        wire:click="switchPlan({{ $p->id }})"
                        style="cursor:pointer;border:1px solid {{ $planId === $p->id ? 'var(--c-fg)' : 'var(--c-border)' }};background:{{ $planId === $p->id ? 'var(--c-fg)' : 'transparent' }};color:{{ $planId === $p->id ? 'var(--c-surface)' : 'var(--c-fg-strong)' }};padding:0.25rem 0.625rem;border-radius:9999px;font-size:var(--text-xs);font-weight:var(--font-weight-semibold);"
                    >
                        #{{ $p->id }} · {{ $p->status }} · {{ $p->duration_days }}d
                    </button>
                @endforeach
            </div>
        @endif

        @if ($plan)
            <div style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;padding:0.75rem 1rem;background:var(--c-surface);border:1px solid var(--c-border);border-radius:var(--radius-lg);">
                <button
                    type="button"
                    wire:click="previousDay"
                    @disabled($currentDay <= 1)
                    style="cursor:pointer;border:1px solid var(--c-border);background:transparent;color:var(--c-fg-strong);padding:0.375rem 0.875rem;border-radius:var(--radius-md);font-size:var(--text-sm);font-weight:var(--font-weight-semibold);{{ $currentDay <= 1 ? 'opacity:0.4;cursor:not-allowed;' : '' }}"
                >
                    ← Prev
                </button>

                <div style="display:flex;flex-direction:column;align-items:center;gap:0.125rem;">
                    <div style="font-size:var(--text-xs);font-weight:var(--font-weight-semibold);text-transform:uppercase;letter-spacing:0.05em;color:var(--c-fg-muted);">Day</div>
                    <div style="font-family:var(--font-mono);font-size:var(--text-xl);font-weight:var(--font-weight-bold);line-height:1;color:var(--c-fg);">
                        {{ $currentDay }} <span style="color:var(--c-fg-muted);font-weight:var(--font-weight-normal);">/ {{ $totalDays }}</span>
                    </div>
                    @if ($plan->start_date)
                        <div style="font-size:var(--text-xs);color:var(--c-fg-muted);">{{ $plan->start_date->copy()->addDays($currentDay - 1)->format('D, M j') }}</div>
                    @endif
                </div>

                <button
                    type="button"
                    wire:click="nextDay"
                    @disabled($currentDay >= $totalDays)
                    style="cursor:pointer;border:1px solid var(--c-border);background:transparent;color:var(--c-fg-strong);padding:0.375rem 0.875rem;border-radius:var(--radius-md);font-size:var(--text-sm);font-weight:var(--font-weight-semibold);{{ $currentDay >= $totalDays ? 'opacity:0.4;cursor:not-allowed;' : '' }}"
                >
                    Next →
                </button>
            </div>

            @if ($totalDays > 1 && $totalDays <= 30)
                <div style="display:flex;flex-wrap:wrap;gap:0.25rem;">
                    @for ($d = 1; $d <= $totalDays; $d++)
                        <button
                            type="button"
                            wire:click="goToDay({{ $d }})"
                            title="Day {{ $d }}"
                            style="cursor:pointer;width:1.75rem;height:1.75rem;border-radius:9999px;border:1px solid {{ $d === $currentDay ? 'var(--c-fg)' : 'var(--c-border)' }};background:{{ $d === $currentDay ? 'var(--c-fg)' : 'transparent' }};color:{{ $d === $currentDay ? 'var(--c-surface)' : 'var(--c-fg-muted)' }};font-size:var(--text-xs);font-weight:var(--font-weight-semibold);"
                        >
                            {{ $d }}
                        </button>
                    @endfor
                </div>
            @endif

            <div wire:loading.delay style="font-size:var(--text-xs);color:var(--c-fg-muted);font-style:italic;">Loading day…</div>

            <div wire:loading.remove style="display:flex;flex-direction:column;gap:1.5rem;">
                <div>
                    <h3 style="margin:0 0 0.625rem 0;font-size:var(--text-sm);font-weight:var(--font-weight-bold);text-transform:uppercase;letter-spacing:0.075em;color:var(--c-fg-strong);">Meals</h3>
                    @if ($mealPlan)
                        @include('filament.meal-plan.contents', ['record' => $mealPlan])
                    @else
                        <div style="font-size:var(--text-sm);color:var(--c-fg-muted);font-style:italic;">No meal plan for day {{ $currentDay }}.</div>
                    @endif
                </div>

                <div>
                    <h3 style="margin:0 0 0.625rem 0;font-size:var(--text-sm);font-weight:var(--font-weight-bold);text-transform:uppercase;letter-spacing:0.075em;color:var(--c-fg-strong);">Workout</h3>
                    @if ($workoutPlan)
                        @include('filament.workout-plan.contents', ['record' => $workoutPlan])
                    @else
                        <div style="font-size:var(--text-sm);color:var(--c-fg-muted);font-style:italic;">No workout for day {{ $currentDay }} (rest day or not generated).</div>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>