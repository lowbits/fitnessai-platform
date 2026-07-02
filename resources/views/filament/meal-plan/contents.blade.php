@php
    /** @var \App\Models\MealPlan $record */
    $record->loadMissing(['meals.recipe']);

    $mealTypeOrder = ['breakfast' => 1, 'lunch' => 2, 'snack' => 3, 'dinner' => 4];
    $meals = $record->meals->sortBy(fn ($m) => $mealTypeOrder[$m->type] ?? 99)->values();

    $emojiFor = [
        'breakfast' => '🍳',
        'lunch' => '🥗',
        'snack' => '🍓',
        'dinner' => '🍽️',
    ];

    $typeColor = [
        'breakfast' => 'warning',
        'lunch' => 'success',
        'snack' => 'info',
        'dinner' => 'primary',
    ];

    // Use the same Meal::thumbnail_url accessor as the mobile API so admin
    // sees exactly what the user sees on their device.

    $nameDiffers = function ($mealName, $recipeName) {
        if (! $recipeName) {
            return false;
        }
        $norm = fn ($s) => mb_strtolower(preg_replace('/[^a-z0-9]+/i', ' ', (string) $s));

        return trim($norm($mealName)) !== trim($norm($recipeName));
    };

@endphp

<style>
    .fit-meal-grid {
        --c-fg: var(--gray-950);
        --c-fg-strong: var(--gray-700);
        --c-fg-muted: var(--gray-500);
        --c-fg-faint: var(--gray-400);
        --c-surface: var(--gray-50);
        --c-border: var(--gray-200);
        --c-border-soft: var(--gray-100);
    }
    :where(.dark) .fit-meal-grid {
        --c-fg: #fff;
        --c-fg-strong: var(--gray-200);
        --c-fg-muted: var(--gray-400);
        --c-fg-faint: var(--gray-500);
        --c-surface: color-mix(in oklab, #fff 5%, transparent);
        --c-border: color-mix(in oklab, #fff 10%, transparent);
        --c-border-soft: color-mix(in oklab, #fff 6%, transparent);
    }
</style>

@if ($meals->isEmpty())
    <p style="color:var(--gray-500);font-style:italic;font-size:var(--text-sm);">No meals on this day.</p>
@else
    <div class="fit-meal-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:1rem;">
        @foreach ($meals as $meal)
            @php
                $img = $meal->thumbnail_url;
                $ingredients = collect($meal->ingredients ?? [])
                    ->filter(fn ($i) => is_array($i) && filled($i['name'] ?? null))
                    ->map(function ($i) {
                        $unit = $i['unit'] ?? null;
                        $amount = $i['amount'] ?? null;
                        $unitLabel = $unit ? __('units.'.$unit) : null;
                        if ($unit === 'to_taste') {
                            return $i['name'].' · '.$unitLabel;
                        }
                        if (filled($amount) && $unit) {
                            return $i['name'].' · '.$amount.' '.$unitLabel;
                        }

                        return $i['name'];
                    })
                    ->values();
                $totalTime = ($meal->prep_time_minutes ?? 0) + ($meal->cook_time_minutes ?? 0);
                $recipe = $meal->recipe;
                $hasMismatch = $recipe && $nameDiffers($meal->name, $recipe->name);
            @endphp

            <x-filament::section :has-content-el="false">
                {{-- Hero image --}}
                <div style="position:relative;aspect-ratio:4/3;overflow:hidden;border-top-left-radius:var(--radius-xl);border-top-right-radius:var(--radius-xl);background-color:var(--c-surface);">
                    <img src="{{ $img }}" alt="{{ $meal->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;" />

                    <div style="position:absolute;top:0.75rem;left:0.75rem;">
                        <x-filament::badge :color="$typeColor[$meal->type] ?? 'gray'">
                            {{ $emojiFor[$meal->type] ?? '🍴' }} {{ $meal->type }}
                        </x-filament::badge>
                    </div>

                    <div style="position:absolute;top:0.75rem;right:0.75rem;">
                        <span style="display:inline-block;padding:0.25rem 0.625rem;border-radius:9999px;background:rgba(17,24,39,0.85);color:#fff;font-size:var(--text-xs);font-weight:var(--font-weight-bold);">
                            {{ number_format((int) $meal->calories) }} kcal
                        </span>
                    </div>
                </div>

                {{-- Body --}}
                <div style="padding:1rem;display:flex;flex-direction:column;gap:0.75rem;">
                    <div>
                        <h4 style="margin:0;font-size:var(--text-base);font-weight:var(--font-weight-bold);line-height:1.3;color:var(--c-fg);">{{ $meal->name }}</h4>
                        <div style="margin-top:0.25rem;display:flex;flex-wrap:wrap;gap:0.375rem;font-size:var(--text-xs);color:var(--c-fg-muted);text-transform:capitalize;">
                            @if ($meal->cuisine) <span>{{ $meal->cuisine }}</span> @endif
                            @if ($meal->cuisine && $meal->format) <span>·</span> @endif
                            @if ($meal->format) <span>{{ str_replace('_', ' ', $meal->format) }}</span> @endif
                            @if ($totalTime > 0)
                                <span>·</span><span>{{ $totalTime }} min</span>
                            @endif
                            @if ($meal->difficulty)
                                <span>·</span><span>{{ $meal->difficulty }}</span>
                            @endif
                        </div>
                    </div>

                    @if ($recipe)
                        @php
                            $recipeUrl = \App\Filament\Resources\RecipeResource::getUrl('view', ['record' => $recipe->slug ?? $recipe->id]);
                        @endphp
                        <div style="font-size:var(--text-xs);color:var(--c-fg-muted);display:flex;align-items:center;gap:0.375rem;flex-wrap:wrap;">
                            <span>🔗 Recipe</span>
                            <a href="{{ $recipeUrl }}" target="_blank" rel="noopener" style="color:{{ $hasMismatch ? '#ef4444' : 'var(--c-fg-strong)' }};font-weight:var(--font-weight-medium);text-decoration:underline;text-decoration-style:dotted;text-underline-offset:2px;">#{{ $recipe->id }} {{ $recipe->name }}</a>
                            @if ($hasMismatch)
                                <x-filament::badge color="danger">name mismatch</x-filament::badge>
                            @endif
                        </div>
                    @else
                        <div style="font-size:var(--text-xs);color:var(--c-fg-faint);font-style:italic;">No recipe linked</div>
                    @endif

                    {{-- Macros row --}}
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;padding:0.625rem;background:var(--c-surface);border-radius:var(--radius-lg);border:1px solid var(--c-border-soft);">
                        <div style="text-align:center;">
                            <div style="font-size:var(--text-xs);font-weight:var(--font-weight-semibold);text-transform:uppercase;letter-spacing:0.05em;color:var(--c-fg-muted);">Protein</div>
                            <div style="font-family:var(--font-mono);font-size:var(--text-sm);font-weight:var(--font-weight-bold);color:#10b981;">{{ (int) $meal->protein_g }}g</div>
                        </div>
                        <div style="text-align:center;border-left:1px solid var(--c-border);border-right:1px solid var(--c-border);">
                            <div style="font-size:var(--text-xs);font-weight:var(--font-weight-semibold);text-transform:uppercase;letter-spacing:0.05em;color:var(--c-fg-muted);">Carbs</div>
                            <div style="font-family:var(--font-mono);font-size:var(--text-sm);font-weight:var(--font-weight-bold);color:#f59e0b;">{{ (int) $meal->carbs_g }}g</div>
                        </div>
                        <div style="text-align:center;">
                            <div style="font-size:var(--text-xs);font-weight:var(--font-weight-semibold);text-transform:uppercase;letter-spacing:0.05em;color:var(--c-fg-muted);">Fat</div>
                            <div style="font-family:var(--font-mono);font-size:var(--text-sm);font-weight:var(--font-weight-bold);color:#f43f5e;">{{ (int) $meal->fat_g }}g</div>
                        </div>
                    </div>

                    @if ($ingredients->isNotEmpty())
                        <div>
                            <div style="font-size:var(--text-xs);font-weight:var(--font-weight-semibold);text-transform:uppercase;letter-spacing:0.05em;color:var(--c-fg-muted);margin-bottom:0.375rem;">
                                Ingredients · {{ $ingredients->count() }}
                            </div>
                            <div style="display:flex;flex-wrap:wrap;gap:0.25rem;">
                                @foreach ($ingredients->take(10) as $name)
                                    <x-filament::badge color="gray">{{ $name }}</x-filament::badge>
                                @endforeach
                                @if ($ingredients->count() > 10)
                                    <span style="font-size:var(--text-xs);color:var(--c-fg-faint);align-self:center;">+{{ $ingredients->count() - 10 }} more</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (! empty($meal->allergens))
                        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.375rem;padding-top:0.75rem;border-top:1px solid var(--c-border-soft);">
                            <span style="font-size:var(--text-xs);font-weight:var(--font-weight-semibold);text-transform:uppercase;letter-spacing:0.05em;color:var(--c-fg-faint);">Contains</span>
                            @foreach ($meal->allergens as $allergen)
                                <x-filament::badge color="warning">{{ $allergen }}</x-filament::badge>
                            @endforeach
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @endforeach
    </div>
@endif