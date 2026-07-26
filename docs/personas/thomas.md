# Thomas — the vegetarian foodie building muscle

**The headline:** 38-year-old vegetarian guy in the gym for the first time properly. Loves cooking, wants every meal to be different, knows his way around a kitchen. Doesn't want celery, fennel, or eggplant ever.

## Who he is

Thomas is mid-career, established. He decided this year to get serious about training — 3 sessions a week at the gym, beginner-level lifts but with the head-space and time to actually engage with it. Day job is desk-bound, so he counts as mainly-sitting outside the gym.

He's 78 kg at 1.83 m — already lean, the goal is muscle gain. Calorie-surplus territory, and lots of protein.

Vegetarian for years — eggs and dairy yes, no fish, no meat. He's the rare vegetarian who can hit high protein because he cooks. Tempeh, seitan, paneer, lentils, edamame, cottage cheese, eggs — he has a real rotation, and he's bored by the standard "tofu and chickpeas" vegetarian crutches.

His attitude to food is **enjoy it**. Cooking is a hobby. Long recipes are fine. New cuisines welcome. Halloumi, paneer, dhal, frittata, gnocchi — bring it on. But specific dislikes are real: celery, fennel, eggplant. Don't put them in his plan.

## What he wants from the app

> Make every meal of my week different. Give me real recipes, not assembled bowls. Cuisines should rotate — indian, italian, mexican, japanese — and I want vegetarian protein that isn't always tofu.

He wants the maximum variety dial. He's the user who'd be insulted by a meal-prep mode.

## What he struggles with

- Hitting protein on a vegetarian diet at a calorie surplus → ambitious target (~155 g on a build-muscle goal).
- Boredom with stereotype vegetarian food → not another tofu stir-fry.
- Missing variety in cuisines → if six dinners are "vegetarian Italian," he disengages.
- The dislikes being subtle → celery hides in soups and stocks, fennel in some breads and seasonings. Eggplant in moussaka, baba ganoush.

## What "good" looks like for Thomas

A week with **6–7 distinct breakfasts**, **7 distinct lunches**, **4–5 distinct snacks**, **7 distinct dinners** — close to fully distinct. Cuisines rotating: shakshuka one day, paneer curry another, halloumi salad, frittata, dahl, edamame ramen, lentil bolognese. Protein sources rotating: eggs, dairy (skyr, cottage cheese, paneer, halloumi), legumes (lentils, beans, edamame), occasional tempeh/seitan. Cooking complexity varies (some recipes 25 min, some 50).

## Profile configuration

```php
[
    'age' => 38,
    'gender' => Gender::MALE,
    'weight_kg' => 78.0,
    'height_cm' => 183,
    'body_goal' => BodyGoal::BUILD_MUSCLE,
    'skill_level' => SkillLevel::BEGINNER,
    'activity_level' => ActivityLevel::MAINLY_SITTING,
    'training_place' => TrainingPlace::GYM,
    'training_sessions_per_week' => 3,
    'dietary_preference' => DietaryPreference::VEGETARIAN,
    'cooking_preference' => CookingPreference::ELABORATE,
    'meal_variety' => MealVariety::HIGH,
    'meal_prep_enabled' => false,
    'selected_meals' => ['breakfast', 'lunch', 'snack', 'dinner'],
    'food_dislikes' => ['sellerie', 'fenchel', 'aubergine'],
    'locale' => 'de',
]
```

## Audit checks specific to Thomas

- **Variety budget (HIGH)**: 6–7 distinct breakfasts, 7 distinct lunches, 4–5 distinct snacks, 7 distinct dinners. Near-zero repeats this week.
- **No meat, no fish**: zero meals contain chicken, turkey, beef, pork, lamb, fish, or seafood in any form. Eggs and dairy are fine.
- **Protein variety**: across the week, at least 5 distinct plant/vegetarian protein sources should appear (e.g., eggs, paneer, halloumi, lentils, edamame, beans, tempeh, seitan, cottage cheese). If 5 of 7 dinners are "tofu + rice", flag.
- **Cuisine variety**: at least 4 distinct cuisines across the week's lunches and dinners combined.
- **No disliked ingredients**: zero meals contain celery (`sellerie`), fennel (`fenchel`), or eggplant (`aubergine`) — including in sub-ingredients (soup bases, sauces). This is the trickiest check.
- **Protein target hit**: ambitious vegetarian protein target (~2 g/kg = ~155 g) within 10g.
- **Calorie target hit**: each day within ±50 kcal of `daily_calories`.
- **Cooking effort**: elaborate cooking allowed — meals with combined prep+cook of 30–60 min are fine and expected at least 2–3× per week. If every meal is sub-15-min, the elaborate dial isn't being respected.
- **Coherent recipes**: no "vegetarian tofu lentil chickpea bowl" piling 4 plant proteins for the sake of macros. Cuisine identity must read clearly.