---
name: test-meal-personas
description: Run the full meal-plan generation pipeline end-to-end against the three fytrr product personas (Lukas, Anna, Thomas) and audit the output for variety budget, dietary compliance, calorie/protein adherence, dislike-respect, and coherence. Use this skill after any change to meal generation — system/user prompt edits, the variety planner, scaling logic, the SaveMealPlanTool, the agent — or whenever you want to fact-check that "meal quality is good." Do NOT use it for workout plans, single-meal replacement, or unrelated AI tooling.
---

# Test the meal planner against the fytrr personas

This skill runs real meal-plan generation (real AI calls) against the three product personas defined in `docs/personas/`, then audits the output. It is the regression check for meal quality.

## Before you start

1. **Read the persona files**:
   - `docs/personas/lukas.md` — LOW variety, omnivore, meal-prep
   - `docs/personas/anna.md` — MEDIUM variety, pescatarian, 3 meals (no snack), weight loss
   - `docs/personas/thomas.md` — HIGH variety, vegetarian, build muscle

   Each has a "Profile configuration" PHP block (exact `UserProfile` factory fields) and an "Audit checks specific to <name>" section. Use both.

2. **Understand the cost**: this triggers real AI calls. 3 personas × 7 days × ~$0.03/day = ~**$0.60 per full run**, plus ~5 minutes of queue worker time.

3. **Confirm a queue worker is running** (`composer run dev` includes one). If not, the dispatched batches will sit in the `nutrition` queue forever.

4. **Never touch real users.** All test data is ephemeral, namespaced with a timestamp, and torn down at the end of the run regardless of outcome. Persona profile data lives in the MD files, never in the prod DB.

---

## Run procedure

### Step 1 – Spin up ephemeral users

For each persona, create a fresh `User` + `UserProfile` + `Plan` via tinker. Use a namespaced email (`audit-{timestamp}-{persona}@fytrr.test`) and `duration_days: 7`. The factory states already exist in `UserFactory::withProfile()` — you'll override the profile fields per the persona's PHP block.

Skeleton (substitute persona-specific overrides from the MD file):

```php
$ts = now()->format('YmdHis');
$personas = [
    'lukas'  => [/* from docs/personas/lukas.md Profile configuration block */],
    'anna'   => [/* from docs/personas/anna.md */],
    'thomas' => [/* from docs/personas/thomas.md */],
];

$runs = [];
foreach ($personas as $slug => $profileFields) {
    $user = User::factory()->create([
        'email' => "audit-{$ts}-{$slug}@fytrr.test",
        'name'  => ucfirst($slug),
        'locale' => $profileFields['locale'] ?? 'de',
    ]);
    UserProfile::factory()->create(array_merge(
        ['user_id' => $user->id],
        $profileFields,
    ));
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'start_date' => now()->startOfDay(),
        'duration_days' => 7,
        'status' => 'active',
    ]);
    $runs[$slug] = ['user' => $user, 'plan' => $plan];
}
```

### Step 2 – Dispatch generation

```php
foreach ($runs as $slug => $run) {
    App\Jobs\GenerateUserMealPlan::dispatch($run['user'], $run['plan']);
}
```

This puts ~3 batch jobs per persona on the `nutrition` queue (3 days + 3 days + 1 day per persona × 3 personas = 9 jobs).

### Step 3 – Wait for completion

Poll `meal_plans` until all 21 day-plans (3 personas × 7 days) are `status = 'generated'`. Use Monitor or a short `until` loop:

```bash
until [ "$(php artisan tinker --execute 'echo MealPlan::whereIn("plan_id", [<plan_ids>])->where("status","generated")->count();')" = "21" ]; do sleep 10; done
```

Or just poll the DB every minute manually. Expect ~3–5 minutes total wall time.

### Step 4 – Audit each persona

For each persona, run the audit. Read the persona's audit-checks section first so you know what's specific to that persona. The **global** checks below apply to all three.

#### Global checks (apply to every persona)

| Check | How to verify | Pass criterion |
|---|---|---|
| Variety budget hit | `SELECT type, COUNT(DISTINCT name) FROM meals JOIN meal_plans ... GROUP BY type` for each persona's plan | Distinct count per slot matches `MealVariety::perSlotDistinctTargets()` for the persona's tier |
| Daily calorie adherence | Compare `meal_plans.total_calories` per day to `getMetabolismData()['daily_calories']` | Every day within **±50 kcal** of target |
| Daily protein adherence | Compare `meal_plans.total_protein_g` per day to `getMetabolismData()['protein_g']` | Every day within **±10 g** of target |
| Repeats are exact | For any meal name appearing more than once in a plan, all instances must share `calories`, `protein_g`, `carbs_g`, `fat_g`, `ingredients` (compare hashes) | No drift — byte-identical reuse |
| Coherent recipes | Spot-read 3 random meals from each persona | Each meal has a clear cuisine identity, no random-mashup ingredients (`MealPlanSystemPrompt` standard) |
| Locale | `meals.name`, `description`, `ingredients[].name` all in user's locale | German persona = German strings |
| No empty/failed days | All 7 `meal_plans` for each persona must be `status = 'generated'` with `>= 3` child meals | No `failed` or `pending` rows |

#### Persona-specific checks

Run the "Audit checks specific to <name>" section from each persona's MD verbatim. Examples:

- **Lukas**: every NEW meal's `prep_time_minutes + cook_time_minutes ≤ 15`, zero meals contain `pilze`/`rosenkohl`/`leber` in ingredients.
- **Anna**: zero meat or poultry, fish ≤3 times, zero `tofu` or `kichererbsen`, no snack slot generated at all.
- **Thomas**: ≥5 distinct vegetarian protein sources across the week, ≥4 distinct cuisines, zero `sellerie`/`fenchel`/`aubergine` (including hidden in sauces), elaborate cooking respected (≥2 meals with prep+cook ≥ 30 min).

For ingredient checks, query the `ingredients` JSON column and match (case-insensitive, German-stem-aware) against the dislike list.

### Step 5 – Report

Produce a structured report for the user. **Lead with pass/fail per persona, then quantitative tables, then any failures with the specific meal/day, then a verdict.**

Sample shape:

```
## Run summary

| Persona | Variety | Calories | Protein | Diet compliance | Dislikes | Cooking | Verdict |
|---|---|---|---|---|---|---|---|
| Lukas   | ✓ 2/2/1/2  | ✓ +12 avg, max +38 | ✓ 156g (target 159) | ✓ omnivore | ✓ clean | ✗ 2 meals >15min | ⚠ Cooking |
| Anna    | ✓ 4/5/-/5  | ✗ -78 avg, day 3 = -130 | ✓ 105g (target 102) | ✓ no meat, 2× fish | ✓ clean | ✓ | ✗ Calories |
| Thomas  | ✓ 7/7/5/7  | ✓ +5 avg | ✗ 142g (target 156) | ✓ vegetarian | ✗ Day 5 lunch has fennel | ✓ | ✗ Protein, dislikes |

## Failures

- **Anna day 3 calorie shortfall**: ...
- **Thomas day 5 lunch (Fenchel-Risotto)**: contains "Fenchel" in ingredients — explicit dislike. ...
- **Lukas day 2 & day 4 cook-time**: ...

## Verdict

<one-paragraph honest read: is the planner shipping a coach-quality plan, or did this change regress something>
```

### Step 6 – Tear down

Regardless of pass/fail, delete the ephemeral users and their cascade:

```php
foreach ($runs as $run) {
    Meal::whereIn('meal_plan_id', MealPlan::where('plan_id', $run['plan']->id)->pluck('id'))->forceDelete();
    MealPlan::where('plan_id', $run['plan']->id)->delete();
    $run['plan']->delete();
    $run['user']->profile?->forceDelete();
    $run['user']->forceDelete();
}
```

If the run crashed mid-way, the namespacing (`audit-<ts>-...@fytrr.test`) makes orphan cleanup trivial via a single `User::where('email', 'like', 'audit-%@fytrr.test')` query — do that proactively if you suspect leftover state from a previous run.

---

## What NOT to do

- **Do not** test by clearing or regenerating plans for real user IDs from prod. Persona definitions live in `docs/personas/*.md`, not in DB rows.
- **Do not** skip the audit step. The point of this skill is the audit, not just generating data.
- **Do not** mark a persona "passing" because the run completed without errors. The bar is the persona-specific checks, not "no PHP exceptions."
- **Do not** add new checks to a single run on the fly. If you find a new failure mode worth catching forever, edit the relevant persona MD file's audit section so the next run catches it too.
- **Do not** add new personas without product approval. Three is intentional — covers diet × goal × variety dial with minimal overlap.

## When to update this skill

Update SKILL.md (not just the personas) when:

- The meal generation pipeline gains a new component worth auditing (e.g., a post-AI scaling step) — add a global check.
- The variety budget definition changes (per-slot targets shift) — update the variety-budget verification.
- New cost or perf characteristics — update the "before you start" section.

When the system is stable, this skill should rarely change. The persona MDs evolve more often as we learn what users actually want.