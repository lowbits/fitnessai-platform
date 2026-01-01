# Workout Tracking API - Vollständige Dokumentation

> **Version:** 1.0  
> **Letzte Aktualisierung:** 31. Dezember 2025

## Inhaltsverzeichnis
1. [Übersicht](#übersicht)
2. [Datenstruktur](#datenstruktur)
3. [API Endpoints](#api-endpoints)
4. [Request/Response Beispiele](#requestresponse-beispiele)
5. [Feldnamen & Validierung](#feldnamen--validierung)
6. [RPE (Rate of Perceived Exertion)](#rpe-rate-of-perceived-exertion)
7. [Migration & Setup](#migration--setup)
8. [Testing](#testing)

---

## Übersicht

Die Workout Tracking API ermöglicht es Benutzern, ihre Trainingseinheiten detailliert zu erfassen mit:
- ✅ **Set-by-Set Tracking** - Jedes Set mit individuellem Gewicht/Reps
- ✅ **RPE Tracking** - Subjektive Belastungsbewertung (1-10)
- ✅ **Notizen** - Auf Workout-, Übungs- und Set-Ebene
- ✅ **Zeiterfassung** - Start- und Endzeit
- ✅ **Gefühlsbewertung** - Wie hat sich das Workout angefühlt (1-5)

---

## Datenstruktur

### 3-Ebenen Hierarchie

```
workout_trackings (Workout Session)
  ├── started_at, completed_at
  ├── notes, feeling_rate
  └── workout_tracking_exercises (Übung)
        ├── exercise_id, order
        ├── notes (Übungs-Notizen)
        └── workout_tracking_sets (Einzelne Sets)
              ├── set_number
              ├── reps, weight, duration
              ├── rpe (1-10)
              └── notes (Set-Notizen)
```

### Tabellen

#### `workout_trackings`
| Spalte | Typ | Beschreibung |
|--------|-----|--------------|
| `id` | bigint | Primary key |
| `user_id` | bigint | FK zu users |
| `workout_plan_id` | bigint | FK zu workout_plans |
| `started_at` | timestamp | Startzeitpunkt |
| `completed_at` | timestamp (nullable) | Abschlusszeitpunkt |
| `notes` | text (nullable) | Workout-Notizen |
| `feeling_rate` | integer (nullable) | Bewertung 1-5 |
| `created_at`, `updated_at` | timestamp | Timestamps |

#### `workout_tracking_exercises`
| Spalte | Typ | Beschreibung |
|--------|-----|--------------|
| `id` | bigint | Primary key |
| `workout_tracking_id` | bigint | FK zu workout_trackings |
| `exercise_id` | bigint | FK zu exercises |
| `order` | integer | Reihenfolge im Workout |
| `notes` | text (nullable) | Übungs-Notizen |
| `created_at`, `updated_at` | timestamp | Timestamps |

**Warum diese Zwischenebene?**
- Speichert die **Reihenfolge** der Übungen
- Ermöglicht **Übungs-spezifische Notizen** (getrennt von Set-Notizen)
- Vermeidet Redundanz (exercise_id müsste sonst in jedem Set gespeichert werden)
- Ermöglicht zukünftige Features (Übung übersprungen, ersetzt, Superset-Gruppierung)

#### `workout_tracking_sets`
| Spalte | Typ | Beschreibung |
|--------|-----|--------------|
| `id` | bigint | Primary key |
| `workout_exercise_id` | bigint | FK zu workout_tracking_exercises |
| `set_number` | integer | Set-Nummer (1, 2, 3, ...) |
| `reps` | integer (nullable) | Wiederholungen |
| `weight` | decimal(8,2) (nullable) | Gewicht in kg |
| `duration` | integer (nullable) | Dauer in Sekunden |
| `rpe` | integer (nullable) | Rate of Perceived Exertion (1-10) |
| `notes` | text (nullable) | Set-Notizen |
| `created_at`, `updated_at` | timestamp | Timestamps |

---

## API Endpoints

Alle Endpoints erfordern Authentifizierung via Sanctum (`auth:sanctum` Middleware).

Base URL: `/api/v2/track/workouts`

| Methode | Endpoint | Beschreibung |
|---------|----------|--------------|
| `GET` | `/` | Liste aller Workout Trackings des Users |
| `POST` | `/` | Neues Workout Tracking erstellen |
| `GET` | `/{id}` | Spezifisches Workout Tracking abrufen |
| `PUT` | `/{id}` | Workout Tracking aktualisieren |
| `DELETE` | `/{id}` | Workout Tracking löschen |

### Authorization
- User kann nur **eigene** Workout Trackings sehen/bearbeiten/löschen
- Zugriff auf fremde Trackings → `403 Forbidden`

---

## Request/Response Beispiele

### POST `/api/v2/track/workouts` - Workout erstellen

**Request:**
```json
{
  "workout_plan_id": 5,
  "started_at": "2025-12-31T08:00:00Z",
  "completed_at": "2025-12-31T09:30:00Z",
  "notes": "Great session, felt strong",
  "feeling_rate": 5,
  "exercises": [
    {
      "exercise_id": 10,
      "order": 1,
      "notes": "Bench Press - good form",
      "sets": [
        {
          "set_number": 1,
          "reps": 12,
          "weight": 60,
          "rpe": 6,
          "notes": "Warm-up"
        },
        {
          "set_number": 2,
          "reps": 10,
          "weight": 70,
          "rpe": 8
        },
        {
          "set_number": 3,
          "reps": 8,
          "weight": 75,
          "rpe": 9,
          "notes": "Close to failure"
        }
      ]
    },
    {
      "exercise_id": 11,
      "order": 2,
      "notes": "Squats - felt heavy",
      "sets": [
        {
          "set_number": 1,
          "reps": 10,
          "weight": 100,
          "rpe": 7
        },
        {
          "set_number": 2,
          "reps": 8,
          "weight": 110,
          "rpe": 9
        }
      ]
    },
    {
      "exercise_id": 12,
      "order": 3,
      "notes": "Plank hold",
      "sets": [
        {
          "set_number": 1,
          "duration": 60,
          "rpe": 7
        },
        {
          "set_number": 2,
          "duration": 45,
          "rpe": 8
        }
      ]
    }
  ]
}
```

**Response:** `201 Created`
```json
{
  "data": {
    "id": 156,
    "workout_plan_id": 5,
    "started_at": "2025-12-31T08:00:00.000000Z",
    "completed_at": "2025-12-31T09:30:00.000000Z",
    "notes": "Great session, felt strong",
    "feeling_rate": 5,
    "exercises": [
      {
        "id": 1,
        "exercise_id": 10,
        "exercise_name": "Bench Press",
        "order": 1,
        "notes": "Bench Press - good form",
        "sets": [
          {
            "id": 1,
            "set_number": 1,
            "reps": 12,
            "weight": "60.00",
            "duration": null,
            "rpe": 6,
            "notes": "Warm-up"
          },
          {
            "id": 2,
            "set_number": 2,
            "reps": 10,
            "weight": "70.00",
            "duration": null,
            "rpe": 8,
            "notes": null
          },
          {
            "id": 3,
            "set_number": 3,
            "reps": 8,
            "weight": "75.00",
            "duration": null,
            "rpe": 9,
            "notes": "Close to failure"
          }
        ]
      },
      {
        "id": 2,
        "exercise_id": 11,
        "exercise_name": "Squats",
        "order": 2,
        "notes": "Squats - felt heavy",
        "sets": [
          {
            "id": 4,
            "set_number": 1,
            "reps": 10,
            "weight": "100.00",
            "duration": null,
            "rpe": 7,
            "notes": null
          },
          {
            "id": 5,
            "set_number": 2,
            "reps": 8,
            "weight": "110.00",
            "duration": null,
            "rpe": 9,
            "notes": null
          }
        ]
      },
      {
        "id": 3,
        "exercise_id": 12,
        "exercise_name": "Plank",
        "order": 3,
        "notes": "Plank hold",
        "sets": [
          {
            "id": 6,
            "set_number": 1,
            "reps": null,
            "weight": null,
            "duration": 60,
            "rpe": 7,
            "notes": null
          },
          {
            "id": 7,
            "set_number": 2,
            "reps": null,
            "weight": null,
            "duration": 45,
            "rpe": 8,
            "notes": null
          }
        ]
      }
    ],
    "workout_plan": {
      "id": 5,
      "workout_name": "Upper Body Strength",
      "workout_type": "strength",
      "date": "2025-12-31"
    },
    "created_at": "2025-12-31T08:00:00.000000Z",
    "updated_at": "2025-12-31T09:30:05.000000Z"
  }
}
```

### GET `/api/v2/track/workouts` - Liste abrufen

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 156,
      "workout_plan_id": 5,
      "started_at": "2025-12-31T08:00:00.000000Z",
      "completed_at": "2025-12-31T09:30:00.000000Z",
      "notes": "Great session",
      "feeling_rate": 5,
      "exercises": [...],
      "workout_plan": {...},
      "created_at": "2025-12-31T08:00:00.000000Z",
      "updated_at": "2025-12-31T09:30:05.000000Z"
    }
  ]
}
```

### PUT `/api/v2/track/workouts/{id}` - Workout aktualisieren

**Request:**
```json
{
  "completed_at": "2025-12-31T09:45:00Z",
  "notes": "Updated notes",
  "feeling_rate": 4,
  "exercises": [...]
}
```

**Response:** `200 OK` (gleiche Struktur wie POST)

### DELETE `/api/v2/track/workouts/{id}` - Workout löschen

**Response:** `204 No Content`

---

## Feldnamen & Validierung

### Workout Level

| Feld | Typ | Required | Validierung |
|------|-----|----------|-------------|
| `workout_plan_id` | integer | ✅ | Must exist in workout_plans |
| `started_at` | datetime | ✅ | Valid ISO 8601 date |
| `completed_at` | datetime | ❌ | Valid date, must be after started_at |
| `notes` | string | ❌ | Max 1000 characters |
| `feeling_rate` | integer | ❌ | Between 1-5 |
| `exercises` | array | ❌ | Array of exercises |

### Exercise Level

| Feld | Typ | Required | Validierung |
|------|-----|----------|-------------|
| `exercise_id` | integer | ✅ | Must exist in exercises |
| `order` | integer | ❌ | >= 0 (default: array index) |
| `notes` | string | ❌ | Max 500 characters |
| `sets` | array | ❌ | Array of sets |

### Set Level

| Feld | Typ | Required | Validierung |
|------|-----|----------|-------------|
| `set_number` | integer | ✅ | >= 1 |
| `reps` | integer | ❌ | >= 0 |
| `weight` | decimal | ❌ | >= 0 (in kg) |
| `duration` | integer | ❌ | >= 0 (in seconds) |
| `rpe` | integer | ❌ | Between 1-10 |
| `notes` | string | ❌ | Max 200 characters |

---

## RPE (Rate of Perceived Exertion)

RPE ist eine subjektive Skala von 1-10, die angibt, wie anstrengend ein Set war:

| RPE | Beschreibung | Reps in Reserve |
|-----|--------------|-----------------|
| 1-2 | Sehr leicht | 8+ RIR |
| 3-4 | Leicht | 6-7 RIR |
| 5-6 | Moderat | 4-5 RIR |
| 7 | Schwer | 3 RIR |
| 8 | Sehr schwer | 2 RIR |
| 9 | Extrem schwer | 1 RIR |
| 10 | Maximales Effort | 0 RIR (Failure) |

**RIR** = Reps in Reserve (wie viele Wiederholungen noch möglich wären)

### Verwendung

```json
{
  "set_number": 3,
  "reps": 8,
  "weight": 100,
  "rpe": 9,
  "notes": "Could have done 1 more rep"
}
```

### Vorteile von RPE Tracking
- ✅ Autoregulation: Training an Tagesform anpassen
- ✅ Übertraining vermeiden
- ✅ Progressionsplanung
- ✅ Vergleichbarkeit über verschiedene Übungen

---

## Migration & Setup

### 1. Migrationen ausführen

```bash
php artisan migrate
```

Dies erstellt folgende Tabellen:
- `workout_trackings`
- `workout_tracking_exercises`
- `workout_tracking_sets`

### 2. Models

Alle Models sind bereits erstellt:
- `App\Models\WorkoutTracking`
- `App\Models\WorkoutTrackingExercise`
- `App\Models\WorkoutTrackingSet`

### 3. Routen

Routen sind registriert in `routes/api.php`:
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('track')->group(function () {
        Route::get('/workouts', [WorkoutTrackingController::class, 'index']);
        Route::post('/workouts', [WorkoutTrackingController::class, 'store']);
        Route::get('/workouts/{workoutTracking}', [WorkoutTrackingController::class, 'show']);
        Route::put('/workouts/{workoutTracking}', [WorkoutTrackingController::class, 'update']);
        Route::delete('/workouts/{workoutTracking}', [WorkoutTrackingController::class, 'destroy']);
    });
});
```

### 4. Routen verifizieren

```bash
php artisan route:list --path=track
```

---

## Testing

### Tests ausführen

```bash
# Alle Workout Tracking Tests
php artisan test tests/Feature/WorkoutTrackingTest.php

# Mit Filter
php artisan test --filter=WorkoutTrackingTest
```

### Test Coverage

Die Test-Suite umfasst:
1. ✅ User can start tracking a workout
2. ✅ User can complete a workout tracking with exercises (mit Sets)
3. ✅ User can get their workout trackings
4. ✅ User can get a specific workout tracking
5. ✅ User can update a workout tracking
6. ✅ User cannot access another user's workout tracking
7. ✅ Workout tracking requires authentication
8. ✅ Workout tracking requires valid workout_plan_id
9. ✅ Feeling rate must be between 1 and 5
10. ✅ User can delete a workout tracking
11. ✅ RPE must be between 1 and 10

---

## Use Cases & Beispiele

### Progressive Overload tracken

```json
"exercises": [{
  "exercise_id": 10,
  "sets": [
    { "set_number": 1, "reps": 10, "weight": 80, "rpe": 7 },
    { "set_number": 2, "reps": 10, "weight": 85, "rpe": 8 },
    { "set_number": 3, "reps": 8, "weight": 90, "rpe": 9 }
  ]
}]
```

### Drop Sets

```json
"sets": [
  { "set_number": 1, "reps": 8, "weight": 100, "rpe": 10, "notes": "To failure" },
  { "set_number": 2, "reps": 10, "weight": 80, "rpe": 9, "notes": "Drop set" },
  { "set_number": 3, "reps": 12, "weight": 60, "rpe": 8, "notes": "Final drop" }
]
```

### Pyramid Training

```json
"sets": [
  { "set_number": 1, "reps": 12, "weight": 60, "rpe": 6 },
  { "set_number": 2, "reps": 8, "weight": 80, "rpe": 9 },
  { "set_number": 3, "reps": 12, "weight": 60, "rpe": 7 }
]
```

### Zeitbasierte Übungen (Plank, Cardio)

```json
"sets": [
  { "set_number": 1, "duration": 60, "rpe": 7 },
  { "set_number": 2, "duration": 50, "rpe": 8 },
  { "set_number": 3, "duration": 40, "rpe": 9 }
]
```

### Bodyweight Übungen

```json
"sets": [
  { "set_number": 1, "reps": 15, "weight": 0, "rpe": 7, "notes": "Bodyweight" },
  { "set_number": 2, "reps": 12, "weight": 0, "rpe": 8 }
]
```

---

## Datenanalyse Möglichkeiten

Mit dieser Struktur können folgende Analysen durchgeführt werden:

### 1. Volumen pro Übung
```php
$volume = $exercise->sets->sum(fn($set) => $set->reps * $set->weight);
```

### 2. Durchschnittliches RPE
```php
$avgRpe = $exercise->sets->avg('rpe');
```

### 3. Personal Records (PRs)
```php
$maxWeight = WorkoutTrackingSet::where('exercise_id', $exerciseId)
    ->where('user_id', $userId)
    ->max('weight');
```

### 4. Volumen-Trend über Zeit
```php
$weeklyVolume = WorkoutTracking::where('user_id', $userId)
    ->whereBetween('started_at', [$startDate, $endDate])
    ->with('exercises.sets')
    ->get()
    ->sum(fn($tracking) => 
        $tracking->exercises->sum(fn($exercise) => 
            $exercise->sets->sum(fn($set) => $set->reps * $set->weight)
        )
    );
```

### 5. Durchschnittliche Erholung zwischen Sets
```php
// Wenn timestamps pro Set getrackt werden (zukünftig)
$restTime = $set2->created_at->diffInSeconds($set1->created_at);
```

---

## Häufige Fragen (FAQ)

### Kann ich ein Workout starten ohne es abzuschließen?

Ja! Setzen Sie einfach `completed_at` auf `null`:
```json
{
  "workout_plan_id": 5,
  "started_at": "2025-12-31T08:00:00Z",
  "completed_at": null
}
```

Später mit PUT aktualisieren.

### Muss ich RPE für jeden Set angeben?

Nein, RPE ist optional. Sie können es nur für wichtige Sets tracken.

### Kann ich Übungen in beliebiger Reihenfolge hinzufügen?

Ja, das `order` Feld bestimmt die Reihenfolge. Wenn nicht angegeben, wird die Array-Index verwendet.

### Was passiert wenn ich ein Workout lösche?

Durch `onDelete('cascade')` werden automatisch alle verknüpften Exercises und Sets gelöscht.

### Kann ich ein Set nachträglich bearbeiten?

Ja, mit PUT können Sie das gesamte Workout inklusive aller Sets aktualisieren.

---

## Troubleshooting

### Error: "Workout plan not found"
- ✅ Prüfen Sie ob die `workout_plan_id` existiert
- ✅ Prüfen Sie ob der Plan dem User gehört

### Error: "Exercise not found"
- ✅ Prüfen Sie ob die `exercise_id` existiert
- ✅ Prüfen Sie ob die Exercise zum workout_plan gehört

### Error: "Forbidden"
- ✅ Versuchen Sie auf ein fremdes Workout zuzugreifen
- ✅ Überprüfen Sie Ihr Auth-Token

### Sets werden nicht gespeichert
- ✅ Prüfen Sie die Struktur: `exercises[].sets[]`
- ✅ Stellen Sie sicher, dass `set_number` gesetzt ist

---

## Changelog

### Version 1.0 (31.12.2025)
- ✅ Set-by-Set Tracking implementiert
- ✅ RPE Support hinzugefügt
- ✅ Vereinfachte Feldnamen (reps, weight, duration)
- ✅ 3-Ebenen Struktur (Tracking → Exercise → Set)
- ✅ Vollständige CRUD API
- ✅ Test Coverage

---

## Support

Bei Fragen oder Problemen:
1. Prüfen Sie diese Dokumentation
2. Führen Sie Tests aus: `php artisan test --filter=WorkoutTrackingTest`
3. Überprüfen Sie die Logs: `storage/logs/laravel.log`

---

**Happy Tracking! 🏋️‍♂️💪**

