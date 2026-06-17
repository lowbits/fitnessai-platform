@php
    /** @var \App\Models\User $record */
    $plan = $record->plans()
        ->where('status', 'active')
        ->latest('created_at')
        ->with([
            'mealPlans' => fn ($q) => $q->where('day_number', 1)->with(['meals.recipe']),
            'workoutPlans' => fn ($q) => $q->where('day_number', 1)->with(['exercises.exercise']),
        ])
        ->first();

    $mealPlan = $plan?->mealPlans->first();
    $workoutPlan = $plan?->workoutPlans->first();

    $mealTypeOrder = ['breakfast' => 1, 'lunch' => 2, 'snack' => 3, 'dinner' => 4];
    $meals = $mealPlan?->meals->sortBy(fn ($m) => $mealTypeOrder[$m->type] ?? 99)->values() ?? collect();
    $exercises = $workoutPlan?->exercises ?? collect();
@endphp

@if (! $plan)
    <div class="text-sm text-gray-500 italic">No active plan for this user.</div>
@else
    <div class="space-y-6">
        <div class="text-xs text-gray-500">
            Plan <span class="font-mono">#{{ $plan->id }}</span>
            · started {{ $plan->start_date?->format('Y-m-d') }}
            · {{ $plan->duration_days }} days
            · {{ $plan->daily_calories }} kcal/day target
        </div>

        {{-- Meals --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Day 1 · Meals</h3>
                @if ($mealPlan)
                    <span class="text-xs text-gray-500">
                        {{ $mealPlan->total_calories }} kcal ·
                        {{ $mealPlan->total_protein_g }}g P ·
                        {{ $mealPlan->total_carbs_g }}g C ·
                        {{ $mealPlan->total_fat_g }}g F
                    </span>
                @endif
            </div>

            @if ($meals->isEmpty())
                <div class="text-sm text-gray-500 italic">No meals generated yet.</div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($meals as $meal)
                        @php
                            $img = $meal->recipe?->image_full ?? $meal->image_full;
                        @endphp
                        <div class="flex gap-3 rounded-lg border border-gray-200 dark:border-gray-700 p-3 bg-white dark:bg-gray-900">
                            <div class="shrink-0 w-16 h-16 rounded-md bg-gray-100 dark:bg-gray-800 overflow-hidden flex items-center justify-center">
                                @if ($img)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img) }}" alt="" class="w-full h-full object-cover" />
                                @else
                                    <span class="text-2xl">
                                        @switch ($meal->type)
                                            @case ('breakfast') 🍳 @break
                                            @case ('lunch') 🥗 @break
                                            @case ('snack') 🍓 @break
                                            @case ('dinner') 🍽️ @break
                                            @default 🍴
                                        @endswitch
                                    </span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] uppercase font-semibold tracking-wider text-gray-500">{{ $meal->type }}</span>
                                    @if ($meal->cuisine)
                                        <span class="text-[10px] text-gray-400">· {{ $meal->cuisine }}</span>
                                    @endif
                                </div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $meal->name }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $meal->calories }} kcal · {{ $meal->protein_g }}g P · {{ $meal->carbs_g }}g C · {{ $meal->fat_g }}g F
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Workout --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Day 1 · Workout</h3>
                @if ($workoutPlan)
                    <span class="text-xs text-gray-500">
                        {{ $workoutPlan->title ?? '—' }}
                        @if ($workoutPlan->duration_minutes) · {{ $workoutPlan->duration_minutes }} min @endif
                    </span>
                @endif
            </div>

            @if ($exercises->isEmpty())
                <div class="text-sm text-gray-500 italic">
                    @if (! $workoutPlan)
                        No workout for day 1 (rest day or not generated yet).
                    @else
                        Workout has no exercises yet.
                    @endif
                </div>
            @else
                <ol class="space-y-1.5">
                    @foreach ($exercises as $idx => $wpe)
                        <li class="flex items-center gap-3 text-sm">
                            <span class="text-xs text-gray-400 font-mono w-5">{{ $idx + 1 }}.</span>
                            <span class="flex-1 text-gray-900 dark:text-gray-100">{{ $wpe->exercise?->name ?? '(missing exercise)' }}</span>
                            <span class="text-xs text-gray-500 font-mono">
                                @if ($wpe->sets && $wpe->reps)
                                    {{ $wpe->sets }}×{{ $wpe->reps }}
                                @elseif ($wpe->duration_seconds)
                                    {{ $wpe->duration_seconds }}s
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>
@endif