@php
    /** @var \App\Models\User $record */
    $profile = $record->profile;
@endphp

<style>
    .fit-targets {
        --c-fg: var(--gray-950);
        --c-fg-strong: var(--gray-700);
        --c-fg-muted: var(--gray-500);
        --c-surface: var(--gray-50);
        --c-border: var(--gray-200);
        --c-border-soft: var(--gray-100);
    }
    :where(.dark) .fit-targets {
        --c-fg: #fff;
        --c-fg-strong: var(--gray-200);
        --c-fg-muted: var(--gray-400);
        --c-surface: color-mix(in oklab, #fff 5%, transparent);
        --c-border: color-mix(in oklab, #fff 10%, transparent);
        --c-border-soft: color-mix(in oklab, #fff 6%, transparent);
    }
    .fit-stat { display:flex; flex-direction:column; gap:0.125rem; padding:0.625rem 0.75rem; background:var(--c-surface); border:1px solid var(--c-border-soft); border-radius:var(--radius-lg); }
    .fit-stat-label { font-size:var(--text-xs); font-weight:var(--font-weight-semibold); text-transform:uppercase; letter-spacing:0.05em; color:var(--c-fg-muted); }
    .fit-stat-value { font-family:var(--font-mono); font-size:var(--text-base); font-weight:var(--font-weight-bold); color:var(--c-fg); line-height:1.2; }
    .fit-stat-sub { font-size:var(--text-xs); color:var(--c-fg-muted); }
</style>

@if (! $profile)
    <p style="color:var(--gray-500);font-style:italic;font-size:var(--text-sm);">No profile — onboarding not completed.</p>
@else
    @php
        try {
            $m = $profile->getMetabolismData();
            $error = null;
        } catch (\Throwable $e) {
            $m = null;
            $error = $e->getMessage();
        }
        $mealSplit = [
            'breakfast' => 0.275,
            'lunch' => 0.325,
            'snack' => 0.125,
            'dinner' => 0.275,
        ];
    @endphp

    @if ($error)
        <p style="color:#ef4444;font-size:var(--text-sm);">Could not compute metabolism: {{ $error }}</p>
    @else
        <div class="fit-targets" style="display:flex;flex-direction:column;gap:1rem;">

            {{-- Metabolism --}}
            <div>
                <h4 style="margin:0 0 0.5rem 0;font-size:var(--text-xs);font-weight:var(--font-weight-bold);text-transform:uppercase;letter-spacing:0.075em;color:var(--c-fg-strong);">Metabolism</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:0.5rem;">
                    <div class="fit-stat">
                        <span class="fit-stat-label">BMR</span>
                        <span class="fit-stat-value">{{ number_format($m['bmr']) }} kcal</span>
                        <span class="fit-stat-sub">base resting</span>
                    </div>
                    <div class="fit-stat">
                        <span class="fit-stat-label">TDEE</span>
                        <span class="fit-stat-value">{{ number_format($m['tdee']) }} kcal</span>
                        <span class="fit-stat-sub">×{{ $m['activity_multiplier'] }} activity</span>
                    </div>
                    <div class="fit-stat">
                        <span class="fit-stat-label">Daily Target</span>
                        <span class="fit-stat-value" style="color:#10b981;">{{ number_format($m['daily_calories']) }} kcal</span>
                        <span class="fit-stat-sub">{{ sprintf('%+d', $m['goal_adjustment']) }} for {{ $m['goal'] }}</span>
                    </div>
                    <div class="fit-stat">
                        <span class="fit-stat-label">Training</span>
                        <span class="fit-stat-value">{{ $m['training_sessions_per_week'] }}×/week</span>
                        <span class="fit-stat-sub">{{ $profile->activity_level?->value }}</span>
                    </div>
                </div>
            </div>

            {{-- Macros --}}
            <div>
                <h4 style="margin:0 0 0.5rem 0;font-size:var(--text-xs);font-weight:var(--font-weight-bold);text-transform:uppercase;letter-spacing:0.075em;color:var(--c-fg-strong);">Daily Macros</h4>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;">
                    <div class="fit-stat" style="border-left:3px solid #10b981;">
                        <span class="fit-stat-label">Protein</span>
                        <span class="fit-stat-value" style="color:#10b981;">{{ $m['protein_g'] }}g</span>
                        <span class="fit-stat-sub">{{ round(($m['protein_g'] * 4 / $m['daily_calories']) * 100) }}% of kcal{{ $m['protein_challenging'] ? ' · ⚠ challenging' : '' }}</span>
                    </div>
                    <div class="fit-stat" style="border-left:3px solid #f59e0b;">
                        <span class="fit-stat-label">Carbs</span>
                        <span class="fit-stat-value" style="color:#f59e0b;">{{ $m['carbs_g'] }}g</span>
                        <span class="fit-stat-sub">{{ $m['carb_fat_ratio']['carbs'] }}% remaining kcal</span>
                    </div>
                    <div class="fit-stat" style="border-left:3px solid #f43f5e;">
                        <span class="fit-stat-label">Fat</span>
                        <span class="fit-stat-value" style="color:#f43f5e;">{{ $m['fat_g'] }}g</span>
                        <span class="fit-stat-sub">{{ $m['carb_fat_ratio']['fat'] }}% remaining kcal{{ $m['minimum_fat_enforced'] ?? false ? ' · floor enforced' : '' }}</span>
                    </div>
                </div>
            </div>

            {{-- Per-slot split --}}
            <div>
                <h4 style="margin:0 0 0.5rem 0;font-size:var(--text-xs);font-weight:var(--font-weight-bold);text-transform:uppercase;letter-spacing:0.075em;color:var(--c-fg-strong);">Per-Meal Split</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:0.5rem;">
                    @foreach ($mealSplit as $slot => $share)
                        @php
                            $cal = (int) round($m['daily_calories'] * $share);
                            $p = (int) round($m['protein_g'] * $share);
                            $c = (int) round($m['carbs_g'] * $share);
                            $f = (int) round($m['fat_g'] * $share);
                            $emoji = ['breakfast' => '🍳', 'lunch' => '🥗', 'snack' => '🍓', 'dinner' => '🍽️'][$slot] ?? '🍴';
                        @endphp
                        <div class="fit-stat">
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <span class="fit-stat-label">{{ $emoji }} {{ $slot }}</span>
                                <span style="font-size:var(--text-xs);color:var(--c-fg-muted);">{{ round($share * 100) }}%</span>
                            </div>
                            <span class="fit-stat-value">{{ $cal }} kcal</span>
                            <span class="fit-stat-sub">P {{ $p }}g · C {{ $c }}g · F {{ $f }}g</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Diet + style --}}
            <div>
                <h4 style="margin:0 0 0.5rem 0;font-size:var(--text-xs);font-weight:var(--font-weight-bold);text-transform:uppercase;letter-spacing:0.075em;color:var(--c-fg-strong);">Diet Resolution</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:0.5rem;">
                    <div class="fit-stat">
                        <span class="fit-stat-label">Preference</span>
                        <span class="fit-stat-value" style="font-family:inherit;font-size:var(--text-sm);text-transform:capitalize;">{{ $m['dietary_preference'] }}</span>
                    </div>
                    <div class="fit-stat">
                        <span class="fit-stat-label">Style</span>
                        <span class="fit-stat-value" style="font-family:inherit;font-size:var(--text-sm);text-transform:capitalize;">{{ $m['diet_style'] ?? '—' }}</span>
                    </div>
                    <div class="fit-stat">
                        <span class="fit-stat-label">Carb/Fat Ratio</span>
                        <span class="fit-stat-value">{{ $m['carb_fat_ratio']['carbs'] }} / {{ $m['carb_fat_ratio']['fat'] }}</span>
                        <span class="fit-stat-sub">after protein</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif