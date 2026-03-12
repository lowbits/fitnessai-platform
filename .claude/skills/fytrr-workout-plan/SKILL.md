---
name: fytrr-workout-plan
description: Generate high-quality fytrr free workout plan config entries as valid PHP arrays. Use this skill whenever Tobi asks to create a new workout plan, add a plan to the config, generate a plan page, or expand the free workout plan section of fytrr. Also use when asked to generate plans for specific goals like strength, fat loss, abs, over-40, cardio, etc. The output is always a complete, production-ready PHP config array entry matching the fytrr freeWorkouts.php structure.
---

# fytrr Free Workout Plan Generator

Generates complete, production-ready PHP config array entries for fytrr's `config/freeWorkouts.php`.

## Before You Start

Read the full structure and quality standards:
→ `references/structure.md`

This file contains:
- The exact PHP array schema (every field, every type)
- Quality standards for each section
- The gold standard reference plans to match
- Common generation mistakes to avoid

Do not skip this step. The structure must be followed exactly.

---

## Generation Process

### Step 1 – Understand the Goal

Identify the plan slug and fitness goal from the user's request. Common examples:
- `strength` → pure strength / powerlifting-adjacent
- `fat-loss` → cutting / body recomposition
- `abs` → core-focused
- `over-40` → age-appropriate, joint-friendly
- `cardio` → endurance / cardiovascular focus
- `hiit` → high-intensity interval training

If the goal is ambiguous, ask one clarifying question before proceeding.

### Step 2 – Plan the Schedule

Before writing the config, mentally design the training split:
- How many days per week makes sense for this goal?
- What split works best? (Full Body / Push-Pull-Legs / Upper-Lower / Goal-specific)
- What duration and equipment fits the audience?
- How many weeks — enough for meaningful progression?

The schedule must be **differentiated from existing plans**. Don't copy the muscle-gain Push/Pull/Legs/Core structure unless it genuinely fits best.

### Step 3 – Generate the Full Config Entry

Output the complete PHP array entry. Follow this order exactly:

1. `title`, `description`, `h1`, `intro`
2. `internal_type`, `published_at`, `last_updated_at`
3. `workout` (with full `schedule`, `progression`, `tips`)
4. `why_it_works` (exactly 5 sections)
5. `common_mistakes` (exactly 7 mistakes with all 5 sub-fields)
6. `faqs` (exactly 3)

### Step 3b – Register the Route Slug

Every new plan **must** be added to the route locale files so Laravel can resolve the URL and generate SEO links (hreflang, footer, sitemap).

- **English:** `lang/en/routes.php` → add to the `'type'` array
- **German:** `lang/de/routes.php` → add to the `'type'` array

**Key:** the `internal_type` (snake_case, e.g. `strength`, `fat_loss`)
**Value:** must **exactly match** the config key used in `config/freeWorkouts.php` for that locale — because the config key IS the URL slug.

Example for a plan with `internal_type` = `strength`:
```php
// config/freeWorkouts.php has 'en' => ['strength' => [...]] and 'de' => ['krafttraining' => [...]]

// lang/en/routes.php → type array
'strength' => 'strength',        // matches EN config key

// lang/de/routes.php → type array
'strength' => 'krafttraining',   // matches DE config key
```

**CRITICAL:** The route value and the `freeWorkouts.php` config key must be identical. If they differ, the page will 404 or SEO links (hreflang, footer, sitemap) will break.

### Step 3c – Add Footer Label (if featured in footer)

If the plan should appear in the site footer, two things are needed:

1. **Add the `internal_type` to the `$workoutPlanTypes` array** in `app/Http/Middleware/HandleInertiaRequests.php` → `getFooterLinks()` method.

2. **Add a footer label translation** in both locale files:
   - `lang/en/footer.php` → `workout_plans` array: `'internal_type' => 'Display Label'`
   - `lang/de/footer.php` → `workout_plans` array: `'internal_type' => 'Anzeigename'`

Example for `strength`:
```php
// lang/en/footer.php → workout_plans array
'strength' => 'Strength Training',

// lang/de/footer.php → workout_plans array (always use "Trainingsplan [Ziel]" pattern)
'strength' => 'Trainingsplan Krafttraining',
```

Without the footer label, the link will be silently skipped even if it's in the `$workoutPlanTypes` array.

### Step 4 – Quality Check (internal)

Before outputting, verify:
- [ ] Every exercise has `sets`, `reps`, AND `rest`
- [ ] Progression phases match the week count
- [ ] `why_it_works` has exactly 5 entries with scientific reasoning
- [ ] `common_mistakes` has exactly 7 entries with real examples (numbers/specifics)
- [ ] `faqs` has exactly 3 entries
- [ ] No vague tips ("eat healthy" → reject; "consume 1.6–2.0g protein/kg" → accept)
- [ ] Goal keyword appears naturally in title, h1, intro, and why_it_works headings
- [ ] "free" appears in title and description

---

## Output Format

Output only the PHP array entry, ready to paste into `config/freeWorkouts.php` under the `'en'` key.

Open with the slug key:
```php
'slug' => [
    // ... full content
],
```

No explanation needed before the code block unless the user asks a question. After the code, offer to also generate the German (`'de'`) version if relevant.

---

## German Plans

If the user requests a German version, follow the same structure but:
- Translate all user-facing strings (title, description, h1, intro, headings, tips, mistakes, faqs)
- Keep `internal_type` in English snake_case
- Keep `published_at` and `last_updated_at` as-is
- Use metric units (kg, cm) — no imperial
- Use formal "Sie" OR informal "du" — match the tone of existing German plans (check `de` key in config)
- Slug goes under the `'de'` key, e.g. `'krafttraining'`
