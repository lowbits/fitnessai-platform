# fytrr Free Workout Plan – Config Structure & Quality Standards

## PHP Config Array Structure

Every plan must follow this exact structure. Do not omit any field.

```php
'slug' => [
    'title'           => 'Free [Goal] Workout Plan – [Duration]',
    'description'     => 'Meta description, 140–160 chars. Include "free", goal keyword, and one concrete benefit.',
    'h1'              => '[Goal] Workout Plan – [Short Value Proposition]',
    'intro'           => '2–3 sentence intro. Mention duration, primary method, and main outcome.',
    'internal_type'   => 'snake_case_goal',   // e.g. strength, fat_loss, abs
    'published_at'    => 'YYYY-MM-DD',
    'last_updated_at' => 'YYYY-MM-DD',

    'workout' => [
        'weeks'              => (int),
        'workouts_per_week'  => (int),
        'duration_minutes'   => (int),
        'level'              => 'Beginner | Beginner to Advanced | All Levels | Intermediate to Advanced',
        'equipment'          => ['array', 'of', 'equipment'],

        'schedule' => [
            [
                'day'       => 'Day N – [Focus Name]',
                'focus'     => 'Short description of muscle groups / training goal',
                'exercises' => [
                    ['name' => '', 'sets' => (int), 'reps' => '', 'rest' => ''],
                    // 4–6 exercises per day
                ],
            ],
            // Repeat for each training day
        ],

        'progression' => 'Weeks 1–N: phase | Weeks N–N: phase | ...',
        'tips'        => [
            // 3–5 concise, actionable nutrition/recovery tips
        ],
    ],

    'why_it_works' => [
        'title'   => 'Why This [Goal] Workout Plan Works',
        'content' => [
            [
                'heading' => '',
                'text'    => '',
            ],
            // Exactly 5 entries
        ],
    ],

    'common_mistakes' => [
        'title'    => 'The [N] Most Common [Goal] Mistakes — and How to Avoid Them',
        'mistakes' => [
            [
                'title'       => '',
                'problem'     => '',
                'consequence' => '',
                'solution'    => '',
                'example'     => '',
            ],
            // Exactly 7 entries
        ],
        'summary' => 'One closing sentence summarising why people fail and what this plan does differently.',
    ],

    'faqs' => [
        ['question' => '', 'answer' => ''],
        ['question' => '', 'answer' => ''],
        ['question' => '', 'answer' => ''],
        // Exactly 3 entries
    ],
],
```

---

## Quality Standards

### Tone & Voice
- Authoritative but accessible — like a knowledgeable coach, not a textbook
- Second-person ("you", "your") throughout
- Concrete over vague: always include numbers, percentages, timeframes, or examples
- British/neutral English (not American-only idioms)

### `why_it_works` — 5 sections, each must:
- Have a bold, benefit-focused heading (not "Section 1")
- Cite or reference scientific reasoning (EPOC, AMDR, progressive overload research etc.) — paraphrase, never fabricate citations
- Be 2–4 sentences. No fluff.
- Cover different angles: e.g. physiology, training science, psychology, nutrition alignment, recovery

### `common_mistakes` — 7 entries, each must:
- `problem`: What the person actually does wrong (specific behaviour)
- `consequence`: The real-world outcome of that mistake (specific, not vague)
- `solution`: Concrete fix (not "do better")
- `example`: A real, numerical or actionable example — e.g. "If maintenance is 2,400 kcal, eat 2,700–2,900 kcal"
- Cover diverse root causes: nutrition, recovery, psychology, technique, programming

### `workout.schedule`
- Day names must be descriptive: "Day 1 – Push" not "Day 1"
- Each exercise needs sets, reps AND rest period
- Reps should use ranges where appropriate: "8–12" not just "10"
- Rest times in seconds: "60s", "90s", "120s"
- 4–6 exercises per day — not more, not less
- Progression must match the week count exactly

### `faqs`
- Questions must be things real users actually ask (check common fitness search queries)
- Answers: 1–2 sentences, direct, no padding

### SEO Considerations (for AI search visibility)
- Use the goal keyword naturally in title, h1, intro and why_it_works headings
- Include "free" in title and description
- Specific numbers and durations signal authority to AI models
- Plain, factual language indexes better than marketing copy

---

## Reference Plans (Gold Standard)

The following slugs already exist and represent the quality bar:
- `muscle-gain` — 12 weeks, 4 days/week, Push/Pull/Legs/Core split
- `weight-loss` — 8 weeks, 3 days/week, Strength + HIIT + Lower Body
- `beginner` — 6 weeks, 3 days/week, Full Body Basics

When generating a new plan, match or exceed this quality level.

---

## Common Mistakes to Avoid When Generating Plans

1. Generic exercise lists — every exercise should make sense for the specific goal
2. Identical structure to existing plans — differentiate the schedule split
3. Vague tips — "eat healthy" is not acceptable; "consume 1.6–2.0g protein per kg bodyweight" is
4. Missing rest periods in exercises
5. Progression that doesn't match the week count
6. Fabricated study citations — reference research fields, not fake papers
7. American-only measurements — use metric primary (kg, cm) with imperial optional
