# Food search & nutrition — architecture draft

## Goal
Premium, fast food search where **calories are always correct** — even for products the OFF bulk dump leaves empty (e.g. Club-Mate: dump `null`, API `20 kcal`). Plus custom foods, and a photo→kcal path.

## Proven constraints (why this shape)
- Import is correct: 12/12 sampled barcodes have `index == dump`. Our pipeline is fine.
- No static export (JSONL/CSV/MongoDB) reliably has nutrition — all snapshot the same DB with the same gaps.
- The **OFF API is the only complete, current source** for nutrition.

## Principle
**MySQL = source of truth. Meilisearch = derived search index. OFF API = nutrition on demand (cached).**

---

## Data model

### `foods` (NEW) — durable home for custom + enriched foods
OFF's 4.4M catalog stays **Meili-only** (rebuildable from the dump). This table holds only what must survive an index rebuild.
```
id
source            FoodSource  (custom | openfoodfacts | ai)
user_id?          -> users     (null = shared/global)
barcode?          (unique per source)
name, brand?
kcal, protein_g, carbs_g, fat_g, fiber_g, sugar_g, sat_fat_g, salt_g   -- per 100 g/ml
serving_size?, serving_unit?
verified          bool
timestamps
index (user_id), unique (source, barcode)
```
Scout-synced into the Meili `foods` index so custom + enriched foods are searchable alongside the catalog.

### `calorie_trackings` (EXISTING) — the log, reused as-is
Already snapshot-based (`calories/protein_g/carbs_g/fat_g` frozen, `external_id` = OFF ref, `meal_type`, `source` accessor). Keep it. Optional later: rename to `food_entries` for clarity, add nullable `food_id -> foods` so custom/enriched logs link to their source. No structural change required now.

### `meal_photos` (NEW, phase 2)
```
id, user_id, image_path, analysis (json), created_at
```

### Meili index `foods` (renamed from `products`)
OFF catalog (dump) + `foods` rows (Scout). Relevance settings below.

---

## Enums
- `MealType` — **exists**, reuse.
- `FoodSource` (NEW) — `Custom`, `OpenFoodFacts`, `Ai`.
- `LogMethod` (optional) — `Search`, `Barcode`, `Photo`, `Manual` (the existing `source` accessor already approximates this).

---

## Services / actions
- `OpenFoodFactsClient` — HTTP wrapper for `GET /api/v2/product/{barcode}`, with a required descriptive `User-Agent` (`fytrr/1.0 (support@fytrr.com)`), timeout, and 429 handling.
- `ResolveFoodNutrition` — index-first; on null nutrition → OFF API → `ProductExtractor::extract()` → upsert into `foods` (→ Scout → Meili) → return full macros. Self-healing.
- `MealPhotoAnalyzer` (agent, phase 2) — Claude vision, structured output `[{name, est_portion_g, confidence}]`; each item resolved against `foods` for authoritative macros, model kcal only as flagged fallback.
- `FoodController` — thin gateway for the endpoints below.

---

## Endpoints (`/v3`)
```
POST /v3/search-token         mint a Meili tenant token (per-user filter, ~24h)
GET  /v3/foods/{barcode}      resolve + enrich + cache -> full food
POST /v3/foods                create a custom food (source=custom, user_id)
POST /v3/calorie-trackings    log a food (snapshot) — existing flow
POST /v3/meal-photos          upload + analyze (phase 2)
```
Search/autocomplete does **not** go through the platform per keystroke — the app queries Meili **directly** with the tenant token (fast + per-user + key hidden).

---

## Flows
- **Search / autocomplete** → app → Meili (tenant token). Filter `source != 'custom' OR user_id = {me}`.
- **Barcode** → `GET /v3/foods/{barcode}` → Meili; if nutrition null → OFF API → cache to `foods` → return. Fixed for everyone after first scan.
- **Custom food** → `POST /v3/foods` → Scout → searchable.
- **Log** → snapshot macros onto `calorie_trackings` (unchanged).
- **Photo** → `POST /v3/meal-photos` → detected foods → review card → log.

---

## Meili relevance settings (apply via `updateSettings`, no re-import)
```
searchableAttributes: [product_name, product_name_de, product_name_en, brands]   // names only; locale-neutral order
rankingRules:         [words, typo, proximity, attribute, sort, exactness, popularity_key:desc]
stopWords:            [de/en common words]
filterable:           [barcode, source, user_id, ...]     // source/user_id enable per-user tenant filter
```
Locale is applied **per query**, not baked into the index: the search sets
`attributesToSearchOn` to the user's language first (e.g. de → `[product_name, product_name_de, brands]`,
en → `[product_name, product_name_en, brands]`), so results follow the user's locale, not a fixed language.

---

## Build order
1. `foods` table + `FoodSource` enum + Scout sync (custom foods durable & searchable).
2. `OpenFoodFactsClient` + `ResolveFoodNutrition` + `GET /v3/foods/{barcode}` (the calorie fix).
3. `POST /v3/search-token` + Meili settings + app switches to tenant-token search; drop `EXPO_PUBLIC_MEILISEARCH_*`.
4. Rename index `products` → `foods`; add `source`/`user_id` to filterable.
5. Phase 2: `MealPhotoAgent` + `meal_photos` + review UI.

## What the user feels
- Calories are right (Club-Mate = 20), and stay right (self-healing, dump-drift-proof).
- "apple" → apple foods, German names first, no ingredient noise.
- Their own custom foods show up in the same search.
- Search stays instant (tenant token, direct).
- New: snap a meal → logged.
