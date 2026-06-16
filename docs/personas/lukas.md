# Lukas — the meal-prep beginner

**The headline:** 23-year-old guy starting his first real training cycle. Wants to build muscle, doesn't want to cook, doesn't want to think about food. Buys his groceries Sunday and reheats for the week.

## Who he is

Lukas is in his early twenties — student or first job, lives alone or with one roommate. Spent most of his life mainly sitting, no real athletic background. He recently joined a gym, found a 3-day plan online, and is committed for the first time. The fitness part he's into. The kitchen part he's not.

He's 95 kg at 1.78 m — carries some softness he'd like to lose while putting on muscle. The body-recomp dream. Coach-mode goal: build muscle, lean recomp follows from training.

His relationship with food is **convenience-first**. He eats what's easy, what's familiar, what he can portion into a tupperware. Picky in a low-key way — mushrooms, brussels sprouts, and liver are immediate no's.

## What he wants from the app

> Tell me what to eat. Make it the same thing most days. Make it fit in containers. Don't make me cook every night.

The product promise that lands for him is **"cook once, eat 3×"**. He should open the plan on Monday and see a small shopping list, three or four recipes, and a clear week of repeats.

## What he struggles with

- Cooking time / energy after the gym → needs **quick** prep (≤15min).
- Variety overload → if every day is a different recipe, he won't actually cook. The plan he ignores is the plan that fails.
- Hitting protein → 95 kg + build-muscle target = high protein. Without coaching, he'd live on pasta.
- Surprise ingredients → if a recipe needs sumac or harissa, it doesn't get bought.

## What "good" looks like for Lukas

A week with **1–2 distinct breakfasts**, **2 distinct lunches**, **1 distinct snack**, **2 distinct dinners**. Most slots are reuses of the same 6 actual recipes. Everything from a standard supermarket. Sunday hint is "this is a prep day — these meals batch well, store 2–3 days."

If on day 4 his lunch is identical to day 1's lunch (same ingredients, same grams), that's the feature, not a bug.

## Profile configuration

```php
[
    'age' => 23,
    'gender' => Gender::MALE,
    'weight_kg' => 95.0,
    'height_cm' => 178,
    'body_goal' => BodyGoal::BUILD_MUSCLE,
    'skill_level' => SkillLevel::BEGINNER,
    'activity_level' => ActivityLevel::MAINLY_SITTING,
    'training_place' => TrainingPlace::GYM,
    'training_sessions_per_week' => 3,
    'dietary_preference' => DietaryPreference::OMNIVORE,
    'cooking_preference' => CookingPreference::QUICK,
    'meal_variety' => MealVariety::LOW,
    'meal_prep_enabled' => true,
    'selected_meals' => ['breakfast', 'lunch', 'snack', 'dinner'],
    'food_dislikes' => ['pilze', 'rosenkohl', 'leber'],
    'locale' => 'de',
]
```

## Audit checks specific to Lukas

When auditing his generated 7-day plan:

- **Variety budget (LOW)**: ≤2 distinct breakfasts, ≤2 distinct lunches, ≤1 distinct snack, ≤2 distinct dinners.
- **Repeats are byte-identical**: a meal reused later in the week has the exact same name, ingredients (same grams), and macros as its first appearance.
- **Cooking time**: every NEW meal's `prep_time_minutes + cook_time_minutes ≤ 15`. Quick-cook constraint must be honored.
- **No disliked ingredients**: zero meals contain mushrooms (`pilze`), brussels sprouts (`rosenkohl`), or liver (`leber`) in any form.
- **Protein target hit**: daily protein within 10g of the metabolism-calculated target (`getMetabolismData()['protein_g']`).
- **Daily calorie adherence**: each day within ±50 kcal of `daily_calories`.
- **Meal-prep framing**: the plan should feel like batched cooking — Sunday or day 1 hints at prep; subsequent days reuse the same Meal records.