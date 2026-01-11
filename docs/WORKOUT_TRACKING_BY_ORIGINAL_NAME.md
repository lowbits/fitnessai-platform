# Workout Tracking by Original Name

## Problem

Das bisherige System hat das letzte Tracking basierend auf der `exercise_id` ermittelt. Da wir jedoch für jeden Workout-Plan neue Exercise-Einträge generieren, haben "Bench Press" am Montag und "Bench Press" am Donnerstag unterschiedliche `exercise_id` Werte, aber denselben `original_name`.

Dies führte dazu, dass Benutzer ihre vorherigen Tracking-Daten (Gewichte, Wiederholungen, etc.) nicht sehen konnten, wenn sie dieselbe Übung an einem anderen Tag durchführten.

## Lösung

Die Lösung verwendet `original_name` statt `exercise_id`, um das letzte Tracking zu finden.

### Implementierung

**File**: `app/Http/Controllers/Api/V2/WorkoutController.php`

Die neue Logik:

1. **Sammle alle `original_name` Werte** der aktuellen Exercises im Workout
2. **Eine einzige performante Query**: Hole alle Tracking-Einträge für diese `original_name` Werte
3. **Gruppiere nach `original_name`**: Wähle für jeden Namen das neueste Tracking (sortiert nach `completed_at`)
4. **Mappe die Ergebnisse**: Weise jedem Exercise das entsprechende letzte Tracking zu

```php
// Get exercises with their original names
$exercises = $workout->exercises()->orderBy('order')->get();
$originalNames = $exercises->pluck('original_name')->unique()->filter()->all();

// Get latest tracking exercises for all original names in one query
$latestTrackings = collect();

if (!empty($originalNames)) {
    $latestTrackings = WorkoutTrackingExercise::query()
        ->select('workout_tracking_exercises.*')
        ->join('workout_trackings', 'workout_tracking_exercises.workout_tracking_id', '=', 'workout_trackings.id')
        ->join('exercises', 'workout_tracking_exercises.exercise_id', '=', 'exercises.id')
        ->where('workout_trackings.user_id', $user->id)
        ->whereIn('exercises.original_name', $originalNames)
        ->whereNotNull('workout_trackings.completed_at')
        ->with(['sets' => fn($query) => $query->orderBy('set_number'), 'exercise:id,original_name', 'workoutTracking:id,completed_at'])
        ->get()
        ->groupBy(fn($item) => $item->exercise->original_name)
        ->map(fn($group) => $group->sortByDesc(fn($item) => $item->workoutTracking->completed_at)->first());
}
```

### Performance-Optimierung

Der existierende Index auf `exercises.original_name` (Migration `2025_12_29_100608_add_original_name_to_exercises_table.php`) stellt sicher, dass die Abfrage performant ist.

**Vorher**: N+1 Problem - Eine Query pro Exercise
**Nachher**: Eine einzige Query für alle Exercises

### Vorteile

1. ✅ **Korrekte Tracking-Historie**: Benutzer sehen ihre letzten Gewichte/Wiederholungen für dieselbe Übung, unabhängig vom Workout-Tag
2. ✅ **Bessere Performance**: Eine Query statt N Queries
3. ✅ **Index-Unterstützung**: Existierender Index auf `original_name` macht die Query schnell
4. ✅ **Skalierbar**: Funktioniert auch bei vielen Übungen und Tracking-Einträgen

### API Response

Das `latest_tracking` Feld in der Exercise-Response enthält nun die letzten Tracking-Daten basierend auf `original_name`:

```json
{
  "id": 123,
  "name": "Bench Press",
  "original_name": "bench_press",
  "latest_tracking": {
    "notes": "Felt strong today",
    "sets": [
      {
        "set_number": 1,
        "reps": 10,
        "weight": 80,
        "duration": null
      },
      {
        "set_number": 2,
        "reps": 8,
        "weight": 85,
        "duration": null
      }
    ]
  }
}
```

### Datenbank-Schema

Der Index auf `exercises.original_name` ist bereits vorhanden:

```php
Schema::table('exercises', function (Blueprint $table) {
    $table->string('original_name')->nullable()->after('name');
    $table->index('original_name'); // ✅ Performance-Index
});
```

## Tests

**File**: `tests/Feature/WorkoutLatestTrackingTest.php`

Umfassende Test-Suite mit 8 Tests, die alle kritischen Szenarien abdecken:

### Test-Szenarien

1. ✅ **Latest tracking across different workouts**: Überprüft, dass Bench Press am Donnerstag die Tracking-Daten vom Montag anzeigt
2. ✅ **Most recent tracking**: Stellt sicher, dass bei mehreren Trackings das neueste (nach `completed_at`) angezeigt wird
3. ✅ **Multiple exercises**: Testet, dass jede Exercise das richtige Tracking bekommt
4. ✅ **Without original_name**: Exercises ohne `original_name` bekommen `null` für `latest_tracking`
5. ✅ **No previous tracking**: Neue Übungen ohne Historie bekommen `null`
6. ✅ **User isolation**: Benutzer sehen nur ihre eigenen Tracking-Daten, nicht die von anderen
7. ✅ **Only completed workouts**: Nur abgeschlossene Workouts (mit `completed_at`) werden berücksichtigt
8. ✅ **Performance with many exercises**: Verifiziert, dass die Query effizient ist (keine N+1 Probleme)

### Tests ausführen

```bash
php artisan test --filter=WorkoutLatestTrackingTest
# oder
./vendor/bin/pest --filter=WorkoutLatestTrackingTest
```

**Ergebnis**: ✅ 8 passed (48 assertions)

## Wichtige Hinweise

- `original_name` muss bei der Exercise-Generierung korrekt gesetzt werden
- Der Wert sollte normalisiert sein (z.B. lowercase, snake_case)
- Exercises ohne `original_name` bekommen kein `latest_tracking` (filter() entfernt null Werte)
- Nur completed Workouts (`completed_at IS NOT NULL`) werden für das Latest Tracking berücksichtigt
- Die Implementierung ist user-isoliert - jeder Benutzer sieht nur seine eigenen Tracking-Daten

