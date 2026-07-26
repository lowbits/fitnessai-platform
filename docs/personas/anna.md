# Anna — the busy pescatarian who wants to lose weight

**The headline:** 30-year-old woman trying to lose weight at home, walking a lot, no time for elaborate cooking. Eats fish, no chicken or red meat. Tofu and chickpeas are out — she doesn't like them.

## Who she is

Anna works a desk-adjacent job that has her on her feet enough to count as walker-level activity. She's 76.5 kg at 1.78 m and wants to come down. She tried calorie-counting apps before but they don't tell her *what* to eat, just whether she fit her budget.

Pescatarian for the last few years — partly health, partly ethics. She'll eat fish and seafood. The usual vegetarian protein crutches (tofu, chickpeas) don't work for her — she's tried, doesn't enjoy them. That leaves: fish, eggs, dairy, lentils, beans, edamame, paneer, halloumi, peas.

She cooks at home most nights but it has to be **normal** — not 5-minute throw-together, not 60-minute prep. Weeknight recipes. She skips snacks; three meals are enough.

## What she wants from the app

> Tell me what to make for dinner that's actually filling at fewer calories, isn't fish every night, doesn't ask me to eat tofu.

She wants enough variety that the week feels interesting, but not so much that grocery shopping is a project. Medium variety is the right dial.

## What she struggles with

- Calorie deficit + satiety → she needs **protein-dense, volume-friendly** meals or she's hungry by 9 pm and snacks.
- Pescatarian variety → easy to fall into "fish three times" rotation. Needs egg + dairy + legume rotation too.
- Skipping snack → all the day's calories live in 3 meals; per-meal calories run higher than a 4-meal split.
- "Healthy" recipes that are 800 kcal salads with too much dressing → the calorie target must actually be hit.

## What "good" looks like for Anna

A week with **3–4 distinct breakfasts**, **4–5 distinct lunches**, **4–5 distinct dinners** (no snack). Fish appears maybe 2× across the week, not daily. Eggs, lentils, beans, halloumi, paneer rotating. Lots of veg, hearty grains, things she can put on the table in 30 minutes after a workday.

## Profile configuration

```php
[
    'age' => 30,
    'gender' => Gender::FEMALE,
    'weight_kg' => 76.5,
    'height_cm' => 178,
    'body_goal' => BodyGoal::LOSE_WEIGHT,
    'skill_level' => SkillLevel::BEGINNER,
    'activity_level' => ActivityLevel::MAINLY_WALKING,
    'training_place' => TrainingPlace::HOME,
    'training_sessions_per_week' => 3,
    'dietary_preference' => DietaryPreference::PESCATARIAN,
    'cooking_preference' => CookingPreference::NORMAL,
    'meal_variety' => MealVariety::MEDIUM,
    'meal_prep_enabled' => false,
    'selected_meals' => ['breakfast', 'lunch', 'dinner'],
    'food_dislikes' => ['tofu', 'kichererbsen'],
    'locale' => 'de',
]
```

## Audit checks specific to Anna

- **Variety budget (MEDIUM, 3 slots)**: ~3–4 distinct breakfasts, ~4–5 distinct lunches, ~4–5 distinct dinners. No snack slot generated at all.
- **No meat or poultry**: zero meals contain chicken, turkey, beef, pork, lamb, or any land animal protein. Fish/seafood is allowed and expected.
- **Fish frequency**: fish appears ≤3 times across the week. If it's the protein in 4+ meals, the plan is monotonous.
- **No disliked proteins**: zero meals contain tofu or chickpeas (`kichererbsen`) in any form. This is non-negotiable — they're her two big "no" buttons.
- **Calorie target hit**: each day within ±50 kcal of `daily_calories`. Lose-weight users feel undershoot/overshoot more sharply than build-muscle users — the deficit is small, drift erodes it.
- **Per-meal calorie distribution**: with 3 slots, calories should redistribute proportionally (no slot tries to fill snack's share).
- **Protein target met**: lean-body-mass-adjusted protein target should be hit within 10g — this matters extra for weight loss to preserve muscle.
- **Satiety hints**: meals should lean toward high-volume, high-protein, high-fiber — not 400 kcal sugary smoothies.