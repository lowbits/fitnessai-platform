# Recipe commands

Tools for keeping the recipe catalog healthy: discover gaps, fill them, dedupe, push to search.

## TL;DR — common workflows

### "My users are seeing bad suggestions / repetitive meals"
```bash
php artisan recipes:audit --locale=de              # spot thin slots
php artisan recipes:seed --diet=vegetarian --meal-type=lunch --locale=de --count=10
php artisan recipes:sync-search                    # push to Meilisearch
php artisan cache:clear                            # bust the 24h suggestions cache
```

### "I added a new dietary preference / tag scheme"
```bash
php artisan recipes:enhance-dietary-tags --dry-run
php artisan recipes:enhance-dietary-tags
php artisan recipes:sync-search
```

### "After a batch of user plans, I want to promote good recipes"
```bash
php artisan recipes:extract --popular --min-count=3
php artisan recipes:sync-search
```

### "First-time deploy / search broken"
```bash
php artisan recipes:setup-search --reset --import   # full nuke + rebuild
```

---

## All commands

| Command | Purpose | When to run |
|---|---|---|
| [`recipes:audit`](#recipesaudit) | Show (diet × meal_type × locale) catalog distribution | Whenever you suspect the pool is uneven, before seeding |
| [`recipes:seed`](#recipesseed) | Proactively AI-generate recipes for a specific slot | When audit shows a thin slot |
| [`recipes:extract`](#recipesextract) | Promote organic user-generated meals → master recipes | Periodically, after real user usage |
| [`recipes:enhance-dietary-tags`](#recipesenhance-dietary-tags) | Cheap-LLM backfill of cascading dietary tags | After changing tag conventions, or on existing recipes that lack tags |
| [`recipes:generate-images`](#recipesgenerate-images) | Generate AI food images for recipes missing them | Periodically, or after a seed/extract run |
| [`recipes:setup-search`](#recipessetup-search) | Configure the Meilisearch `recipes` index | First-time setup or after schema/filter changes |
| [`recipes:sync-search`](#recipessync-search) | Push recipe data to Meilisearch | After any catalog change |

---

### `recipes:audit`

Show how many recipes exist per `(diet, meal_type)` cell. Highlights `0`-count cells in red so thin slots are obvious.

```bash
php artisan recipes:audit                # all locales
php artisan recipes:audit --locale=de    # one locale
```

**Output example:**
```
Locale: de
 diet         breakfast  lunch  snack  dinner
 vegan        3          0      1      0
 vegetarian   8          0      5      2
 pescatarian  5          2      3      4
 omnivore     12         8      10     15
```

`0` cells = users with that diet will see thin or repetitive suggestions. Seed those.

---

### `recipes:seed`

Generate fresh recipes for a specific `(diet, meal_type, locale)` via AI. Cheap (cents per run), uses the same dedup + image-gen pipeline as plan generation.

```bash
php artisan recipes:seed \
    --diet=vegetarian \
    --meal-type=lunch \
    --locale=de \
    --count=10 \
    --dry-run                            # preview without writing
```

**Options:**
- `--diet` — `vegan` | `vegetarian` | `pescatarian` | `omnivore` (required)
- `--meal-type` — `breakfast` | `lunch` | `snack` | `dinner` (required)
- `--locale` — defaults to `en`
- `--count` — defaults to `5`
- `--dry-run` — show generated names + dedup matches without writing

**What happens per recipe:**
1. AI generates with same schema as plan-gen (ingredients with canonical units, allergens, primary_protein, cuisine, format, cascading dietary tags).
2. `RecipeDeduplicator::findSimilar` checks the existing catalog — near-duplicates are skipped.
3. New ones are saved as `Recipe` rows (no Meal/Plan needed).
4. `GenerateRecipeImage` is dispatched async.

Costs roughly **$0.01–0.10** per `--count=10` run depending on model.

---

### `recipes:extract`

Promote unique organic meals (generated for real user plans) into the master `recipes` table. Reactive growth based on actual usage.

```bash
php artisan recipes:extract                              # all unique meals
php artisan recipes:extract --popular --min-count=3      # only meals that occurred ≥3 times
php artisan recipes:extract --meal-type=dinner           # one slot
php artisan recipes:extract --ids=1,5,23                 # specific meal IDs
php artisan recipes:extract --dry-run                    # preview
php artisan recipes:extract --fresh                      # ⚠ wipes the recipes table first
```

Uses Meilisearch hybrid search to dedupe identical/near-identical meals across users so you don't import the same recipe 50 times.

---

### `recipes:enhance-dietary-tags`

Use a cheap LLM to add cascading dietary tags (`vegan` / `vegetarian` / `pescatarian` / `omnivore`) to existing recipes that lack them. The model sees the full ingredient list so it catches edge cases (chicken broth in a "tofu" curry, fish sauce in Thai dishes, rennet-based cheeses, honey in vegan candidates).

```bash
php artisan recipes:enhance-dietary-tags --dry-run
php artisan recipes:enhance-dietary-tags
```

Run once after first adopting the dietary-tag convention. New recipes from `recipes:seed` and the plan-gen path get tags automatically.

---

### `recipes:generate-images`

Generate food images for recipes that don't have one yet.

```bash
php artisan recipes:generate-images           # all missing
php artisan recipes:generate-images --limit=20
```

(See `--help` for the full option list — has filters by primary_protein, locale, etc.)

---

### `recipes:setup-search`

Configure the Meilisearch `recipes` index: filterable attributes, searchable attributes, embedders. Required before any other search-using command will work on a fresh install.

```bash
php artisan recipes:setup-search --import      # configure + push all recipes
php artisan recipes:setup-search               # only reconfigure (after adding filterable fields)
php artisan recipes:setup-search --reset --import   # ⚠ delete index + rebuild
```

Run after changing filterable attributes in `SetupMeilisearchRecipesCommand.php`.

---

### `recipes:sync-search`

Push all recipes to Meilisearch. Run after `recipes:seed`, `recipes:extract`, or `recipes:enhance-dietary-tags` so the search-using endpoints see the new data.

```bash
php artisan recipes:sync-search
```

---

## When suggestions still look wrong after a change

Two layers to bust:

```bash
php artisan recipes:sync-search    # 1. Meilisearch sees the new data
php artisan cache:clear            # 2. The 24h cache on /recipes/suggestions
```

If your filter still returns nothing, check `storage/logs/laravel.log` — `[RecipeFinder]` and `[RecipeSuggestions]` log lines will tell you whether Meilisearch returned zero or threw an error.