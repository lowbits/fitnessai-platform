# fytrr personas

These are the three product personas fytrr designs for. Use them when:

- evaluating whether a new feature actually serves the people we built the app for,
- auditing the quality of generated meal/workout plans against real-user expectations (not abstract metrics),
- making a UX/copy/onboarding call where you need to picture an actual user.

| Persona | One-line | Goal | Diet | Variety dial |
|---|---|---|---|---|
| [Lukas](lukas.md) | 23m meal-prep beginner who doesn't want to cook | Build muscle | Omnivore | LOW + meal prep |
| [Anna](anna.md) | 30f busy pescatarian losing weight at home, no snack, no tofu | Lose weight | Pescatarian | MEDIUM |
| [Thomas](thomas.md) | 38m vegetarian foodie wanting every meal different | Build muscle | Vegetarian | HIGH |

Each persona file has:

1. A narrative section — who they are, what they want, what they struggle with.
2. A `Profile configuration` PHP block — the exact `UserProfile` factory fields that materialize the persona in code.
3. An `Audit checks` section — what to verify when generating plans for this persona. These checks are persona-specific (e.g. Lukas's plan should *prefer* repeats; Thomas's should *avoid* them).

## How they get used

- **By humans**: read them when arguing about features. "Would Lukas actually use this?"
- **By the `/test-meal-personas` Claude skill** (see `.claude/skills/test-meal-personas/SKILL.md`): the skill spins up ephemeral users from the profile blocks, runs the meal-generation pipeline, and audits the output against each persona's checks plus global ones.

## Editing

Profile fields are a single source of truth — when you change a persona's diet or variety dial, the skill picks it up next run. Keep enum names exactly as written (e.g. `BodyGoal::BUILD_MUSCLE`, not `build_muscle`) so the PHP block stays paste-ready.