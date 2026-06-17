<?php

namespace App\Console\Commands;

use App\Enums\Allergen;
use App\Enums\DietaryPreference;
use App\Enums\HeroVeg;
use App\Enums\MealVariety;
use App\Enums\PrimaryProtein;
use App\Jobs\GenerateUserMealPlan;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserProfile;
use App\Testing\PersonaDefinitions;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class TestPersonas extends Command
{
    protected $signature = 'test:personas
        {--analyze : Audit the generated plans against persona-specific + global checks}
        {--keep : Keep the ephemeral users/plans after the run (skip teardown)}
        {--timeout=600 : Max seconds to wait for generation to complete}';

    protected $description = 'Spin up the 3 fytrr personas, generate their meal plans, optionally audit, tear down.';

    public function handle(): int
    {
        $personas = PersonaDefinitions::all();
        $ts = now()->format('YmdHis');

        $this->info('▶ Creating ephemeral users + dispatching generation…');
        $runs = collect($personas)->mapWithKeys(fn (array $fields, string $slug) => [
            $slug => $this->createPersonaRun($slug, $fields, $ts),
        ])->all();

        $this->table(
            ['Persona', 'User', 'Plan', 'Email'],
            collect($runs)->map(fn ($r, $slug) => [$slug, $r['user_id'], $r['plan_id'], $r['email']])->values()->all()
        );

        $planIds = array_column($runs, 'plan_id');

        $this->info('⏳ Waiting for queue to process 21 meal_plans…');
        $ok = $this->waitForGeneration($planIds, (int) $this->option('timeout'));

        if (! $ok) {
            $this->warn('Timed out or some days failed. Continuing with whatever generated.');
        } else {
            $this->info('✓ All 21 days generated.');
        }

        if ($this->option('analyze')) {
            $this->newLine();
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info('  AUDIT');
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            foreach ($runs as $slug => $run) {
                $this->auditPersona($slug, $personas[$slug], $run);
            }
        }

        if (! $this->option('keep')) {
            $this->newLine();
            $this->info('▶ Tearing down ephemeral data…');
            $this->teardown($runs);
            $this->info('✓ Clean.');
        } else {
            $this->newLine();
            $this->warn('--keep set: ephemeral data left in place. Clean up later with:');
            $this->line('  User::where("email","like","audit-%@fytrr.test")->forceDelete()');
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $fields
     * @return array{user_id: int, plan_id: int, email: string}
     */
    private function createPersonaRun(string $slug, array $fields, string $ts): array
    {
        $locale = $fields['locale'] ?? 'de';
        $profileFields = $fields;
        unset($profileFields['locale']);

        $email = "audit-{$ts}-{$slug}@fytrr.test";

        $user = User::factory()->create([
            'email' => $email,
            'name' => ucfirst($slug),
            'locale' => $locale,
        ]);

        UserProfile::factory()->create(array_merge(['user_id' => $user->id], $profileFields));

        $plan = Plan::factory()->create([
            'user_id' => $user->id,
            'start_date' => now()->startOfDay(),
            'duration_days' => 7,
            'status' => 'active',
        ]);

        GenerateUserMealPlan::dispatch($user, $plan);

        return ['user_id' => $user->id, 'plan_id' => $plan->id, 'email' => $email];
    }

    /**
     * @param  list<int>  $planIds
     */
    private function waitForGeneration(array $planIds, int $timeoutSeconds): bool
    {
        $start = time();
        $expected = count($planIds) * 7;

        while (time() - $start < $timeoutSeconds) {
            $done = MealPlan::whereIn('plan_id', $planIds)
                ->whereIn('status', ['generated', 'failed'])
                ->count();

            if ($done >= $expected) {
                $generated = MealPlan::whereIn('plan_id', $planIds)->where('status', 'generated')->count();

                return $generated === $expected;
            }

            sleep(5);
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $personaFields
     * @param  array{user_id: int, plan_id: int, email: string}  $run
     */
    private function auditPersona(string $slug, array $personaFields, array $run): void
    {
        $this->newLine();
        $this->line("┌ {$slug}");

        $profile = UserProfile::where('user_id', $run['user_id'])->first();
        $target = $profile->getMetabolismData();
        $meals = Meal::whereIn('meal_plan_id', MealPlan::where('plan_id', $run['plan_id'])->pluck('id'))->get();
        $mealPlans = MealPlan::where('plan_id', $run['plan_id'])->orderBy('day_number')->get();

        $checks = [
            $this->checkGeneration($mealPlans),
            $this->checkVarietyBudget($meals, $personaFields['meal_variety'], $personaFields['selected_meals']),
            $this->checkCalories($mealPlans, $target['daily_calories']),
            $this->checkProtein($mealPlans, $target['protein_g']),
            $this->checkTemplateTwins($meals),
            $this->checkDietaryCompliance($meals, $personaFields['dietary_preference']),
            $this->checkDislikes($meals, $personaFields['food_dislikes']),
            $this->checkAllergenVocab($meals),
            $this->checkHeroVegMixedRate($meals),
        ];

        $this->table(
            ['Check', 'Result', 'Detail'],
            array_map(fn ($c) => [$c['name'], $c['ok'] ? '✓' : '✗', $c['detail']], $checks)
        );

        $failed = array_filter($checks, fn ($c) => ! $c['ok']);
        $this->line('└ '.(empty($failed) ? '✓ PASS' : '✗ '.count($failed).' check(s) failed'));
    }

    /**
     * @return array{name: string, ok: bool, detail: string}
     */
    private function checkGeneration(Collection $mealPlans): array
    {
        $generated = $mealPlans->where('status', 'generated')->count();
        $failed = $mealPlans->where('status', 'failed')->count();

        return [
            'name' => 'Generation',
            'ok' => $generated === 7 && $failed === 0,
            'detail' => "{$generated}/7 generated, {$failed} failed",
        ];
    }

    /**
     * @param  list<string>  $selectedSlots
     * @return array{name: string, ok: bool, detail: string}
     */
    private function checkVarietyBudget(Collection $meals, MealVariety $variety, array $selectedSlots): array
    {
        $targets = $variety->perSlotDistinctTargets();
        $actual = $meals->groupBy('type')->map(fn (Collection $bySlot) => $bySlot->pluck('name')->unique()->count())->all();

        $issues = [];
        foreach ($selectedSlots as $slot) {
            $target = $targets[$slot] ?? 0;
            $count = $actual[$slot] ?? 0;
            if ($count !== $target) {
                $issues[] = "{$slot}={$count} (target {$target})";
            }
        }

        return [
            'name' => 'Variety budget',
            'ok' => empty($issues),
            'detail' => empty($issues)
                ? collect($selectedSlots)->map(fn ($s) => "{$s}=".($actual[$s] ?? 0))->implode(' ')
                : implode(' ', $issues),
        ];
    }

    /**
     * @return array{name: string, ok: bool, detail: string}
     */
    private function checkCalories(Collection $mealPlans, int $target): array
    {
        $generated = $mealPlans->where('status', 'generated');
        if ($generated->isEmpty()) {
            return ['name' => 'Daily kcal (±50)', 'ok' => false, 'detail' => 'no days'];
        }

        $drifts = $generated->map(fn ($mp) => (int) $mp->total_calories - $target);
        $maxAbs = $drifts->map(fn ($d) => abs($d))->max();
        $worst = $drifts->sort(fn ($a, $b) => abs($b) <=> abs($a))->first();

        return [
            'name' => 'Daily kcal (±50)',
            'ok' => $maxAbs <= 50,
            'detail' => "target {$target}, worst drift ".sprintf('%+d', $worst),
        ];
    }

    /**
     * @return array{name: string, ok: bool, detail: string}
     */
    private function checkProtein(Collection $mealPlans, int $target): array
    {
        $generated = $mealPlans->where('status', 'generated');
        if ($generated->isEmpty()) {
            return ['name' => 'Daily protein (±10g)', 'ok' => false, 'detail' => 'no days'];
        }

        $drifts = $generated->map(fn ($mp) => (int) $mp->total_protein_g - $target);
        $maxAbs = $drifts->map(fn ($d) => abs($d))->max();
        $worst = $drifts->sort(fn ($a, $b) => abs($b) <=> abs($a))->first();

        return [
            'name' => 'Daily protein (±10g)',
            'ok' => $maxAbs <= 10,
            'detail' => "target {$target}g, worst drift ".sprintf('%+d', $worst).'g',
        ];
    }

    /**
     * Plan-wide template-twin check: no two DISTINCT meals (different names)
     * may share {protein, format, hero_veg}. Two meals with the same name are
     * exact repeats, not twins, and that's allowed.
     *
     * @return array{name: string, ok: bool, detail: string}
     */
    private function checkTemplateTwins(Collection $meals): array
    {
        $distinctMeals = $meals->unique('name');

        $twins = $distinctMeals
            ->groupBy(fn (Meal $m) => $m->primary_protein.'|'.$m->format.'|'.$m->hero_veg)
            ->filter(fn (Collection $group) => $group->count() > 1);

        if ($twins->isEmpty()) {
            return ['name' => 'No template-twins (plan-wide)', 'ok' => true, 'detail' => 'clean'];
        }

        $examples = $twins->map(fn (Collection $group, string $key) => $group->pluck('name')->implode(' ↔ ').
            " [{$key}]")->take(3)->implode('; ');

        return [
            'name' => 'No template-twins (plan-wide)',
            'ok' => false,
            'detail' => $twins->count().' twin group(s): '.$examples,
        ];
    }

    /**
     * @return array{name: string, ok: bool, detail: string}
     */
    private function checkDietaryCompliance(Collection $meals, DietaryPreference $preference): array
    {
        $allowed = array_map(fn ($p) => $p->value, PrimaryProtein::allowedFor($preference));
        $violations = $meals->filter(fn (Meal $m) => $m->primary_protein && ! in_array($m->primary_protein, $allowed, true));

        if ($violations->isEmpty()) {
            return ['name' => 'Dietary compliance', 'ok' => true, 'detail' => $preference->value];
        }

        return [
            'name' => 'Dietary compliance',
            'ok' => false,
            'detail' => $violations->count().' violation(s): '.$violations->pluck('name')->take(3)->implode(', '),
        ];
    }

    /**
     * @param  list<string>  $dislikes
     * @return array{name: string, ok: bool, detail: string}
     */
    private function checkDislikes(Collection $meals, array $dislikes): array
    {
        if (empty($dislikes)) {
            return ['name' => 'Dislike compliance', 'ok' => true, 'detail' => 'none configured'];
        }

        $hits = [];
        foreach ($meals as $meal) {
            $haystack = mb_strtolower($meal->name.' '.json_encode($meal->ingredients ?? []));
            foreach ($dislikes as $dislike) {
                if (str_contains($haystack, mb_strtolower($dislike))) {
                    $hits[] = "\"{$meal->name}\" contains \"{$dislike}\"";
                    break;
                }
            }
        }

        return [
            'name' => 'Dislike compliance',
            'ok' => empty($hits),
            'detail' => empty($hits) ? 'clean ('.implode(', ', $dislikes).')' : count($hits).' hit(s): '.implode('; ', array_slice($hits, 0, 3)),
        ];
    }

    /**
     * @return array{name: string, ok: bool, detail: string}
     */
    private function checkAllergenVocab(Collection $meals): array
    {
        $canonical = array_map(fn ($a) => $a->value, Allergen::cases());
        $bad = [];

        foreach ($meals as $meal) {
            foreach ($meal->allergens ?? [] as $allergen) {
                if (! in_array($allergen, $canonical, true)) {
                    $bad[$allergen] = ($bad[$allergen] ?? 0) + 1;
                }
            }
        }

        if (empty($bad)) {
            return ['name' => 'Allergen vocab (EU 14)', 'ok' => true, 'detail' => 'all canonical'];
        }

        $detail = collect($bad)->map(fn ($n, $v) => "\"{$v}\"×{$n}")->take(5)->implode(', ');

        return ['name' => 'Allergen vocab (EU 14)', 'ok' => false, 'detail' => $detail];
    }

    /**
     * @return array{name: string, ok: bool, detail: string}
     */
    private function checkHeroVegMixedRate(Collection $meals): array
    {
        $total = $meals->count();
        if ($total === 0) {
            return ['name' => 'hero_veg specificity', 'ok' => true, 'detail' => 'no meals'];
        }

        $mixed = $meals->whereIn('hero_veg', [HeroVeg::MIXED->value, HeroVeg::NONE->value])->count();
        $rate = round(($mixed / $total) * 100);

        // "ok" threshold: ≤50% of meals get mixed/none. Above that the dedup is weaker.
        return [
            'name' => 'hero_veg specificity',
            'ok' => $rate <= 50,
            'detail' => "{$mixed}/{$total} ({$rate}%) are mixed/none",
        ];
    }

    /**
     * @param  array<string, array{user_id: int, plan_id: int, email: string}>  $runs
     */
    private function teardown(array $runs): void
    {
        $planIds = array_column($runs, 'plan_id');
        $userIds = array_column($runs, 'user_id');
        $mealPlanIds = MealPlan::whereIn('plan_id', $planIds)->pluck('id');

        Meal::whereIn('meal_plan_id', $mealPlanIds)->forceDelete();
        MealPlan::whereIn('plan_id', $planIds)->delete();
        Plan::whereIn('id', $planIds)->delete();
        UserProfile::whereIn('user_id', $userIds)->forceDelete();
        User::whereIn('id', $userIds)->forceDelete();
    }
}
