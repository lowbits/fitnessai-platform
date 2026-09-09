# Marco — the omnivore with a soy + nut allergy who got served tofu & peanut sauce

**The headline:** 29-year-old man, gym 4×/week, building muscle, eats meat. His profile has **`food_dislikes: ["soy", "nuts", "crustaceans", "molluscs"]`** (allergen categories picked at onboarding). He shops at a discounter (Aldi), likes convenience products, and cannot stand tofu or bulgur. His generated plan served him **tofu with peanut sauce and nut-butter breakfasts** — the exact allergens he flagged.

> This persona is a **regression baseline** modelled on a real trial user (prod user #1646) who started a free trial, chatted with the coach for a few minutes, and cancelled immediately. The 7-day plan below is **real production output**. Several audit checks in this file **fail against the current pipeline** — that is intentional. They pass once the allergen-dislike, meal-swap, and omnivore-bias bugs are fixed.

## Who he is

Marco trains hard and eats for it (~167 g protein/day, 2083 kcal target, 4 gym sessions). Desk job otherwise. He's not a cook — he assembles. His fridge is discounter staples: chicken, lean beef, eggs, cheese, skyr. He eats **breakfast, lunch, dinner — no snack**.

He carries a real constraint: **soy, nuts, crustaceans and molluscs are on his block list** (`food_dislikes`). On top of that he told the coach in chat he hates **tofu** and **bulgur** (tofu is soy, so it's doubly out). He's an omnivore who does not want plant-meat substitutes standing in for his protein.

## What he wants from the app

> Give me lunch and dinner I can actually buy at Aldi. Chicken, lean beef, eggs, cheese. No soy, no nuts — and definitely no tofu or bulgur.

Low variety is fine — repeats are a feature. What matters: every meal is meat/egg/dairy-forward, quick to assemble, discounter-shoppable, and **free of his flagged allergens**.

## What he struggles with

- **His allergen list is ignored by the plan generator.** `food_dislikes` holds allergen categories (`soy`, `nuts`), but the finder matches them as literal ingredient names — so tofu (soy) and peanut/nut-butter (nuts) sail straight through.
- **Omnivore ≠ "everything, including tofu".** Omnivore permits all proteins with no meat weighting, so a vegetarian tofu/lentil/bulgur rotation fills half his week.
- **Stating a dislike in chat does nothing.** He typed "Ich hasse Bulgur und Tofu"; nothing was persisted, and the swap suggestions were tofu again.
- **The swap made it worse:** asking for a dinner "ohne Tofu, ohne Erdnüsse" returned 5 cards, all tofu, 4 tofu + peanut, plus a breakfast served as dinner.

## What "good" looks like for Marco

A week of **meat/egg/dairy-forward** meals, LOW variety (repeats allowed), **zero soy, zero nuts, zero tofu, zero bulgur**, quick assembly, discounter-shoppable. Exactly like the *good half* of his real plan (eggs / roast beef / pork), applied to all 7 days.

---

## Baseline: what actually happened (prod user #1646)

Real profile (2026-09-09): `dietary_preference = omnivore`, `food_dislikes = ["soy","nuts","crustaceans","molluscs"]`, `disliked_recipe_ids = [162, 171]`, `cooking_preference = quick`, `meal_variety = low`, `selected_meals = [breakfast, lunch, dinner]`. Targets: **2083 kcal / 167 g protein**.

The plan alternated **two rotations**. One is exactly right for him; the other violates his allergen list every time.

**❌ Bad rotation (days 09, 11, 13, 15 — 4 of 7 days):**

| Slot | Meal | Protein tag | kcal | Violation |
|---|---|---|---|---|
| Breakfast | Skyr-Bowl mit Haferflocken, Beeren und **Nussmus** | dairy | 648 | **nuts** (nut butter) |
| Lunch | Linsen-**Bulgur**-Salat mit Halloumi | dairy | 775 | **bulgur** (chat dislike) + vegetarian for an omnivore |
| Dinner | **Tofu**-Brokkoli-Pfanne mit Reis und **Erdnuss**sauce | tofu | 655 | **soy** (tofu) + **nuts** (peanut) |

**✅ Good rotation (days 10, 12, 14 — 3 of 7 days):**

| Slot | Meal | Protein tag | kcal |
|---|---|---|---|
| Breakfast | Rührei mit Spinat, Hüttenkäse/Feta und Vollkorntoast | eggs | 646–648 |
| Lunch | Roastbeef mit Rosmarinkartoffeln und grünen Bohnen | beef | 770 |
| Dinner | Schweinefilet mit Ofenkartoffeln und Brokkoli | pork | 652 |

So on **4 of 7 days** he was served an allergen-violating, vegetarian rotation; the generator proved on the other 3 days it can do exactly what he needs.

### Then the coach's "fix" made it worse

He asked for a dinner **"ohne Tofu, ohne Erdnüsse"**. `ProposeMealAlternativesTool` returned **5 cards, all 5 tofu, 4 of them tofu + peanut**, incl. a baked-oatmeal *breakfast* offered as dinner. The parallel "bigger lunch" swap mixed 286–290 kcal sandwich cards into a ~775 kcal slot.

### Root causes (see GitHub issues)

1. **Allergen-category dislikes not enforced in generation/swaps.** `RecipeFinder::buildFilter` (`app/Services/Recipe/RecipeFinder.php:136-138`) filters `ingredient_names != "soy"` — a literal ingredient-name match that never catches member ingredients (tofu, Erdnuss, Nussmus). The correct `allergens != X` pattern already exists in `GetRecipeSuggestions.php:107-108` but the main finder doesn't use it. → *allergen-dislike bug*
2. **Meal-swap `wish` is a positive semantic query** and drops slot/calorie filters (`MealAlternatives.php:99-100`). → *meal-swap `wish` bug*
3. **Omnivore = all proteins allowed, no meat weighting** (`PrimaryProtein::allowedFor`). → *omnivore-served-vegetarian bug*
4. **Chat-stated dislikes (bulgur, tofu) are never persisted** to `food_dislikes` — no coach tool writes them. → *persist-dislikes-from-chat feature*

**Open question:** `disliked_recipe_ids = [162, 171]` — if either is the repeating tofu or bulgur recipe, then explicit recipe-level dislikes are also being ignored on reuse.

---

## Profile configuration

```php
[
    'age' => 29,
    'gender' => Gender::MALE,
    'weight_kg' => 88.0,
    'height_cm' => 182,
    'body_goal' => BodyGoal::BUILD_MUSCLE,
    'skill_level' => SkillLevel::INTERMEDIATE,
    'activity_level' => ActivityLevel::MAINLY_SITTING,
    'training_place' => TrainingPlace::GYM,
    'training_sessions_per_week' => 4,
    'dietary_preference' => DietaryPreference::OMNIVORE,
    'cooking_preference' => CookingPreference::QUICK,
    'meal_variety' => MealVariety::LOW,
    'selected_meals' => ['breakfast', 'lunch', 'dinner'], // no snack
    'food_dislikes' => ['soy', 'nuts', 'crustaceans', 'molluscs'],
    'locale' => 'de',
]
```

## Audit checks specific to Marco

> Several of these currently **fail** — they encode the bugs the baseline exposes. A failure here means "not fixed yet," not "flaky test."

- **Allergen-dislike respected (the headline check)**: zero meals contain soy (incl. **tofu**, edamame, soy sauce), nuts (incl. **Nussmus**, **Erdnuss**/peanut, almond, walnut), crustaceans, or molluscs — in name, `ingredients`, sauces, or the `allergens` tag. Non-negotiable. This is the check the real plan fails on 4/7 days.
- **Omnivore is meat-forward**: lunch + dinner mains are built on animal protein (chicken, turkey, beef, pork, fish, eggs, dairy). Plant-protein mains (tofu / tempeh / seitan / legume-as-main) appear **≤1×** across the week and never as a default dinner.
- **No bulgur**: zero meals contain bulgur (his chat-stated grain dislike).
- **Quick cooking respected (QUICK)**: every meal's `prep_time_minutes + cook_time_minutes ≤ 15`.
- **Discounter-shoppable**: spot-check mains use mainstream discounter ingredients (chicken breast, hack, eggs, skyr/quark, cheese, pasta) — nothing that breaks the "buyable at Aldi" promise.
- **LOW variety, no snack**: ~2–3 distinct breakfasts, ~2–3 lunches, ~2–3 dinners; **no snack slot generated**. Repeats expected.
- **Calorie + protein targets hit**: each day within ±50 kcal of `daily_calories` and ±10 g of the protein target.

### Swap-path checks (not covered by `test:personas`; verify manually or in a swap test)

- A swap `wish` of "ohne Tofu, ohne Erdnüsse" must return **zero** tofu and zero peanut cards.
- A swap must stay in the **same meal slot** as the original unless the user explicitly asks to change slot.
- A swap `wish` asking for a **bigger** meal must not return cards far below the original's calories.
