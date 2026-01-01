# Workout Tracking Implementation

## Übersicht

Das Workout Tracking System ermöglicht es Benutzern, ihre Workouts zu verfolgen, einschließlich aller Übungen, Sätze, Wiederholungen, Gewichte und persönlicher Notizen.

## Datenbankstruktur

### Tabellen

#### 1. `workout_trackings`
Haupttabelle für ein getracktes Workout.

**Spalten:**
- `id` - Primärschlüssel
- `user_id` - Fremdschlüssel zu users
- `workout_plan_id` - Fremdschlüssel zu workout_plans
- `started_at` - Startzeit des Workouts
- `completed_at` - Abschlusszeit des Workouts (nullable)
- `notes` - Allgemeine Notizen zum Workout (nullable)
- `feeling_rate` - Bewertung 1-5, wie sich der Benutzer gefühlt hat (nullable)
- `timestamps`

#### 2. `workout_tracking_exercises`
Verknüpfungstabelle zwischen Tracking und einzelnen Übungen.

**Spalten:**
- `id` - Primärschlüssel
- `workout_tracking_id` - Fremdschlüssel zu workout_trackings
- `exercise_id` - Fremdschlüssel zu exercises
- `order` - Reihenfolge der Übung im Workout
- `notes` - Notizen zur spezifischen Übung in diesem Workout (nullable)
- `timestamps`

**Warum benötigt?**
- Ein Workout kann mehrere Übungen enthalten
- Jede Übung kann in unterschiedlicher Reihenfolge ausgeführt werden
- Übungsspezifische Notizen für dieses spezielle Workout

#### 3. `workout_tracking_sets`
Einzelne Sätze pro Übung mit detaillierten Leistungsdaten.

**Spalten:**
- `id` - Primärschlüssel
- `workout_tracking_exercise_id` - Fremdschlüssel zu workout_tracking_exercises
- `set_number` - Satznummer (1, 2, 3, etc.)
- `reps` - Anzahl der Wiederholungen (nullable)
- `weight` - Gewicht in kg (nullable, decimal 8,2)
- `duration` - Dauer in Sekunden für zeitbasierte Übungen (nullable)
- `rpe` - Rate of Perceived Exertion 1-10 (nullable)
- `notes` - Notizen zum spezifischen Satz (nullable)
- `timestamps`

**Warum benötigt?**
- Jede Übung kann mehrere Sätze haben
- Jeder Satz kann unterschiedliche Werte haben (Gewicht, Wiederholungen, etc.)
- Ermöglicht präzises Tracking der Leistung über Zeit

## API Endpoints

Alle Endpoints befinden sich unter `/api/v2/track/workouts` und erfordern Authentifizierung.

### 1. Liste aller Workout-Trackings
```http
GET /api/v2/track/workouts
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "workout_plan_id": 5,
      "started_at": "2026-01-01T10:00:00.000000Z",
      "completed_at": "2026-01-01T11:30:00.000000Z",
      "notes": "Great workout!",
      "feeling_rate": 5,
      "exercises": [...],
      "workout_plan": {...},
      "created_at": "2026-01-01T10:00:00.000000Z",
      "updated_at": "2026-01-01T11:30:00.000000Z"
    }
  ]
}
```

### 2. Workout-Tracking erstellen
```http
POST /api/v2/track/workouts
Authorization: Bearer {token}
Content-Type: application/json

{
  "workout_plan_id": 5,
  "started_at": "2026-01-01T10:00:00Z",
  "completed_at": "2026-01-01T11:30:00Z",
  "notes": "Great workout!",
  "feeling_rate": 5,
  "exercises": [
    {
      "exercise_id": 1,
      "order": 1,
      "notes": "Felt strong",
      "sets": [
        {
          "set_number": 1,
          "reps": 12,
          "weight": 50.5,
          "rpe": 7
        },
        {
          "set_number": 2,
          "reps": 10,
          "weight": 55.0,
          "rpe": 8
        },
        {
          "set_number": 3,
          "reps": 8,
          "weight": 57.5,
          "rpe": 9
        }
      ]
    },
    {
      "exercise_id": 2,
      "order": 2,
      "notes": "Good cardio",
      "sets": [
        {
          "set_number": 1,
          "duration": 300
        }
      ]
    }
  ]
}
```

**Validierung:**
- `workout_plan_id` - required, muss existieren
- `started_at` - required, Datum
- `completed_at` - optional, Datum, muss nach started_at sein
- `notes` - optional, max 1000 Zeichen
- `feeling_rate` - optional, integer 1-5
- `exercises[].exercise_id` - required, muss existieren
- `exercises[].sets[].set_number` - required, min 1
- `exercises[].sets[].reps` - optional, min 0
- `exercises[].sets[].weight` - optional, min 0
- `exercises[].sets[].duration` - optional, min 0
- `exercises[].sets[].rpe` - optional, integer 1-10

### 3. Einzelnes Workout-Tracking abrufen
```http
GET /api/v2/track/workouts/{id}
Authorization: Bearer {token}
```

### 4. Workout-Tracking aktualisieren
```http
PUT /api/v2/track/workouts/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "completed_at": "2026-01-01T11:30:00Z",
  "notes": "Updated notes",
  "feeling_rate": 4,
  "exercises": [...]
}
```

### 5. Workout-Tracking löschen
```http
DELETE /api/v2/track/workouts/{id}
Authorization: Bearer {token}
```

## Beispiel Datenfluss

### Szenario: Benutzer trackt ein Bench Press Workout

1. **Workout starten:**
```json
POST /api/v2/track/workouts
{
  "workout_plan_id": 5,
  "started_at": "2026-01-01T10:00:00Z"
}
```

2. **Während des Workouts Übungen hinzufügen:**
```json
PUT /api/v2/track/workouts/1
{
  "exercises": [
    {
      "exercise_id": 1, // Bench Press
      "order": 1,
      "sets": [
        {"set_number": 1, "reps": 12, "weight": 50},
        {"set_number": 2, "reps": 10, "weight": 55},
        {"set_number": 3, "reps": 8, "weight": 60}
      ]
    }
  ]
}
```

3. **Workout abschließen:**
```json
PUT /api/v2/track/workouts/1
{
  "completed_at": "2026-01-01T11:30:00Z",
  "notes": "Personal best on last set!",
  "feeling_rate": 5
}
```

## Datenbank-Beziehungen

```
User (1) ─── (N) WorkoutTracking
WorkoutPlan (1) ─── (N) WorkoutTracking
WorkoutTracking (1) ─── (N) WorkoutTrackingExercise
Exercise (1) ─── (N) WorkoutTrackingExercise
WorkoutTrackingExercise (1) ─── (N) WorkoutTrackingSet
```

## Models

### WorkoutTracking
- `fillable`: user_id, workout_plan_id, started_at, completed_at, notes, feeling_rate
- `casts`: started_at, completed_at (datetime), feeling_rate (integer)
- Beziehungen: user(), workoutPlan(), exercises()

### WorkoutTrackingExercise
- `fillable`: workout_tracking_id, exercise_id, order, notes
- `casts`: order (integer)
- Beziehungen: workoutTracking(), exercise(), sets()

### WorkoutTrackingSet
- `fillable`: workout_tracking_exercise_id, set_number, reps, weight, duration, rpe, notes
- `casts`: set_number, reps, duration, rpe (integer), weight (decimal:2)
- Beziehungen: workoutTrackingExercise()

## Tests

Alle Tests befinden sich in `tests/Feature/WorkoutTrackingTest.php`:

1. ✅ User kann Workout-Tracking starten
2. ✅ User kann Workout mit Übungen und Sätzen abschließen
3. ✅ User kann seine Workout-Trackings abrufen
4. ✅ User kann einzelnes Workout-Tracking abrufen
5. ✅ User kann Workout-Tracking aktualisieren
6. ✅ User kann nicht auf fremde Workout-Trackings zugreifen
7. ✅ Authentifizierung ist erforderlich
8. ✅ Validation für workout_plan_id
9. ✅ Validation für feeling_rate (1-5)
10. ✅ User kann Workout-Tracking löschen

## Migrationen ausführen

```bash
php artisan migrate:fresh --seed
```

## Tests ausführen

```bash
php artisan test --filter=WorkoutTrackingTest
```

