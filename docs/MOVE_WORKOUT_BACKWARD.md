# Move Workout Backward (Tomorrow to Today)

## Feature Status: ✅ SUPPORTED

Die `RescheduleWorkoutController` erlaubt das Verschieben von Workouts zu jedem Datum `>= heute`.

### Aktuelles Verhalten

**Validation in RescheduleWorkoutRequest:**
```php
'target_date' => [
    'required',
    'date',
    'date_format:Y-m-d',
    'after_or_equal:today', // Allows today or future
],
```

**Controller Validation:**
- Prüft: Nicht das gleiche Datum
- Prüft: Innerhalb der Plan-Duration
- **NICHT** mehr: "Cannot reschedule to past date" (redundant, FormRequest prüft bereits)

### Use Case ✅ FUNKTIONIERT

**Szenario:**
- Heute: Rest Day
- Morgen: Strength Workout
- User möchte: Workout von morgen auf heute verschieben

**Lösung:**
```bash
POST /api/v2/workouts/{workout}/reschedule
{
  "target_date": "2026-01-12",  // Heute
  "force": true                  // Replace heute's rest day
}
```

**Ergebnis:**
- ✅ Morgen wird Rest Day
- ✅ Heute wird Strength Workout
- ✅ Alle Exercises werden verschoben

### Lösungsvorschläge

#### Option 1: Validation lockern (nur echte Vergangenheit verbieten)

```php
// Allow rescheduling to today or future, but not to actual past
if ($targetDate->lt(now()->startOfDay())) {
    return response()->json([
        'error' => 'Invalid operation',
        'message' => 'Cannot reschedule to a past date',
        'target_date' => $targetDate->format('Y-m-d'),
    ], 400);
}
```

**Problem:** Code ist bereits so! Das bedeutet man kann **NICHT** von morgen auf heute verschieben, weil `now()` als Grenze gilt.

#### Option 2: Validation komplett entfernen für vergangene Tage

```php
// Remove the past date check entirely
// Allow rescheduling to any date within plan duration
```

**Vorteil:**
- ✅ Ermöglicht Verschieben von morgen auf heute
- ✅ Ermöglicht Korrektur von bereits vergangenen Workouts

**Nachteil:**
- ⚠️ User könnte versehentlich Workouts in die Vergangenheit verschieben

#### Option 3: Nur "echte Vergangenheit" verbieten (gestern und früher)

```php
// Allow rescheduling to today or future
if ($targetDate->lt(now()->startOfDay())) {
    return response()->json([
        'error' => 'Invalid operation',
        'message' => 'Cannot reschedule to dates before today',
        'target_date' => $targetDate->format('Y-m-d'),
        'today' => now()->format('Y-m-d'),
    ], 400);
}
```

**Problem:** Das ist der aktuelle Code! `now()->startOfDay()` = heute 00:00

### Empfohlene Lösung

**Entferne die past date Validation vollständig:**

```php
private function validateTargetDate(WorkoutPlan $workout, Carbon $targetDate, int $targetDayNumber): ?JsonResponse
{
    $plan = $workout->plan;

    // Cannot reschedule to the same day
    if ($targetDate->isSameDay($workout->date)) {
        return response()->json([
            'error' => 'Invalid operation',
            'message' => 'Cannot reschedule to the same date',
            'current_date' => $workout->date->format('Y-m-d'),
        ], 400);
    }

    // REMOVE: Cannot reschedule to a past date check
    // This allows moving workouts backward (e.g., tomorrow to today)

    // Must be within plan duration
    if ($targetDayNumber < 1 || $targetDayNumber > $plan->duration_days) {
        return response()->json([
            'error' => 'Invalid operation',
            'message' => 'Target date is outside plan duration',
            'target_date' => $targetDate->format('Y-m-d'),
            'plan_start_date' => $plan->start_date->format('Y-m-d'),
            'plan_end_date' => $plan->end_date->format('Y-m-d'),
        ], 400);
    }

    return null;
}
```

**Vorteile:**
- ✅ Ermöglicht Verschieben von morgen auf heute
- ✅ Ermöglicht Verschieben von Tag 10 auf Tag 5
- ✅ User kann flexibel innerhalb des Plans umorganisieren
- ✅ Validation für plan duration bleibt erhalten

**Nachteile:**
- ⚠️ User könnte versehentlich auf vergangene Tage verschieben
- ➡️ Lösung: Frontend sollte vergangene Tage nicht als Option anzeigen

### Tests

Neue Tests hinzufügen:

```php
test('user can move workout from tomorrow to today', function () {
    // Today: Rest day
    $todayRestDay = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now(),
        'day_number' => 1,
        'workout_type' => 'rest',
    ]);

    // Tomorrow: Strength workout
    $tomorrowWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDay(),
        'day_number' => 2,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
    ]);

    Exercise::factory()->count(3)->create([
        'workout_plan_id' => $tomorrowWorkout->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$tomorrowWorkout->id}/reschedule", [
            'target_date' => now()->format('Y-m-d'),
            'force' => true, // Force replace today's rest day
        ]);

    $response->assertStatus(200);

    // Verify tomorrow is now rest day
    $tomorrowWorkout->refresh();
    expect($tomorrowWorkout->workout_type)->toBe('rest');

    // Verify today has the strength workout
    $todayWorkout = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('day_number', 1)
        ->where('workout_type', 'strength')
        ->first();
    
    expect($todayWorkout)->not->toBeNull();
    expect($todayWorkout->workout_name)->toBe('Push Day');
    expect($todayWorkout->exercises()->count())->toBe(3);
});

test('cannot move workout to past date (yesterday)', function () {
    $futureWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$futureWorkout->id}/reschedule", [
            'target_date' => now()->subDay()->format('Y-m-d'), // Yesterday
        ]);

    $response->assertStatus(400)
        ->assertJson([
            'error' => 'Invalid operation',
            'message' => 'Cannot reschedule to a past date',
        ]);
});
```

### API Endpoint

**Existing:**
```
POST /api/v2/workouts/{workout}/reschedule
```

**Request:**
```json
{
  "target_date": "2026-01-12",  // Can be today or any future date within plan
  "force": true                  // Optional: Replace existing workout on target date
}
```

**Response (Success):**
```json
{
  "message": "Workout rescheduled successfully",
  "rest_day": {
    "id": 123,
    "date": "2026-01-13",
    "name": "Rest Day",
    "type": "rest"
  },
  "rescheduled_workout": {
    "id": 456,
    "date": "2026-01-12",
    "name": "Push Day",
    "type": "strength",
    "exercises_count": 7
  }
}
```

### Fazit

**Aktuelle Implementierung:** ❌ Erlaubt NICHT das Verschieben rückwärts

**Empfehlung:** Entferne die "past date" validation, aber behalte:
- ✅ "Cannot reschedule to same date" check
- ✅ "Must be within plan duration" check
- ✅ Frontend sollte past dates nicht anbieten

