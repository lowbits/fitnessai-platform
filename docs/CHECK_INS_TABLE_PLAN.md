# Plan: Dedicated `check_ins` table (separate the event from the measurement log)

## Motivation

Today a weekly check-in is scattered across `body_progress`:

- `LogWeightTool` inserts a `body_progress` row (weight + optional note).
- `UpdateCheckInTool` heuristically loads *today's most recent `body_progress` row* and writes measurements, `mood`, `energy`, and appends the note onto it.
- `mood`/`energy` were bolted onto `body_progress` via `2026_08_21_090000_add_mood_energy_to_body_progress_table` (nullable tinyints).

Two problems:

1. **Concern bleed.** `body_progress` is the physical measurement log (weight, circumferences) read by the weight-trend chart, `BodyProgressController`, and `TrackingChartWidget`. `mood`/`energy` are wellbeing signals that only exist during a check-in — they sit null on every non-check-in weight log.
2. **No first-class event.** There's a `WeeklyCheckinNotification` + `SendWeeklyCheckins` nudge, but nothing records that a check-in *happened*. "Did the user complete their weekly check-in?" (for streaks / perfect-day / Mona's signal→plan loop) can only be inferred heuristically.

## Design principle

**Keep physical facts in `body_progress`; give the check-in event its own home.**

- `body_progress` stays the source of truth for weight + measurements (other features read them there — do **not** move these).
- New `check_ins` table owns the event: when it happened, mood, energy, and a reference to the weigh-in row. The free-text note stays on `body_progress.notes` (reachable via the link), so no note display regresses and the note isn't duplicated across two tables.

## Scope

Backend only (`platform` repo). No frontend change required — the widgets already submit as natural-language chat; only the tools that persist change. Verify no mobile client reads `body_progress.mood`/`.energy` back (grep shows nothing does today).

---

## 1. Migration — create `check_ins`

`database/migrations/2026_08_24_000000_create_check_ins_table.php`

```php
Schema::create('check_ins', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('body_progress_id')->nullable()
        ->constrained('body_progress')->nullOnDelete();
    $table->unsignedTinyInteger('mood')->nullable()->comment('1 rough … 5 great');
    $table->unsignedTinyInteger('energy')->nullable()->comment('1 very low … 5 very high');
    $table->timestamp('checked_in_at');
    $table->timestamps();

    $table->index(['user_id', 'checked_in_at']);
});
```

`body_progress_id` is nullable so a mood-only check-in (user skips the scale) is still a valid event.

## 2. Migration — retire mood/energy from `body_progress`

`database/migrations/2026_08_24_000100_drop_mood_energy_from_body_progress_table.php`

Reverse of the Aug-21 add. No backfill needed — prod is provisioned with a fresh migrate, not incrementally migrated, so there are no legacy mood/energy rows to carry over.

```php
public function up(): void {
    Schema::table('body_progress', fn (Blueprint $t) => $t->dropColumn(['mood', 'energy']));
}
public function down(): void {
    Schema::table('body_progress', function (Blueprint $t) {
        $t->unsignedTinyInteger('mood')->nullable()->after('notes');
        $t->unsignedTinyInteger('energy')->nullable()->after('mood');
    });
}
```

The check-in wellbeing note moves to `check_ins.note`; `body_progress.notes` stays for measurement-context notes.

## 3. Model — `app/Models/CheckIn.php`

```php
class CheckIn extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'body_progress_id', 'mood', 'energy', 'note', 'checked_in_at'];
    protected $casts = ['mood' => 'integer', 'energy' => 'integer', 'checked_in_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function bodyProgress(): BelongsTo { return $this->belongsTo(BodyProgress::class); }
}
```

- `User`: add `public function checkIns(): HasMany` (and `latestCheckIn` convenience if useful for streaks).
- `BodyProgress`: remove `mood`/`energy` from `$fillable` and `$casts`; add optional `checkIn(): HasOne`.
- Add `BodyProgressFactory` cleanup + a `CheckInFactory`.

## 4. `LogWeightTool` — create the event as the anchor

After inserting the `body_progress` row, create today's `check_ins` row (or reuse if one already exists for today) and link it:

```php
$entry = $this->user->bodyProgress()->create([
    'weight_kg' => $weight,
    'recorded_at' => now(),
    'notes' => $note !== '' ? $note : null,
]);

$this->user->checkIns()->updateOrCreate(
    ['checked_in_at' => today()],            // one check-in per day; adjust key if weekly
    ['body_progress_id' => $entry->id],
);
```

Decide the dedupe window: `whereDate('checked_in_at', today())` keeps it simple; a true weekly window can come later. The wellbeing `note` from this tool should route to `check_ins.note` rather than `body_progress.notes` (consolidate with step 5).

## 5. `UpdateCheckInTool` — split the write

- **Measurements** (`waist_cm`, `hip_cm`, …, `body_fat_percent`, `muscle_mass_kg`) → still update the linked `body_progress` row.
- **mood / energy / note** → update today's `check_ins` row.

Anchor on today's `check_ins` row (find-or-create, `updateOrCreate` keyed on `checked_in_at = today()`) for mood/energy; measurements and the appended note still write to today's `body_progress` row. Keep the 1–5 clamp for mood/energy. A mood-only answer with no weigh-in records a scale-less check-in (`body_progress_id` null).

Error copy stays: if no check-in exists yet, tell Mona to `log_weight` (or a future `start_check_in` write) first.

## 6. Tests (Pest, `tests/Feature`)

Mirror existing tool tests:

- `log_weight` creates both a `body_progress` row and a linked `check_ins` row.
- `update_check_in` writes mood/energy to `check_ins`, measurements + note to `body_progress`.
- Second `update_check_in` / weigh-in the same day updates the same check-in (no duplicate).
- mood/energy clamp to 1–5.
- `update_check_in` before any weigh-in returns the existing error; mood-only records a scale-less event.

## 7. Downstream wiring (follow-up, not blocking)

- Point streak / perfect-day logic at `check_ins` for "checked in this week."
- Optional `CheckInController` / API resource if the app should read wellbeing history back (none reads it today).

---

## Trade-off / when NOT to do this

If the only consumer stays "store two numbers," the current two nullable columns are a defensible shortcut (YAGNI). This plan pays off the moment streaks or Mona need "a check-in happened this week" as a discrete, queryable signal — which the product vision points to soon. It also removes the fragile "today's most recent body_progress row" heuristic in favor of an explicit event row.

## Rough size

Small–medium: 2 migrations + 1 backfill, 1 new model + 2 model edits, 2 tool edits, ~6 tests. No frontend work.
