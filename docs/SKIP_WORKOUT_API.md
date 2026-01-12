# Skip Workout API

## Übersicht

Die Skip Workout API ermöglicht es Benutzern, ein geplantes Workout zu überspringen. Das Workout wird durch einen Ruhetag (Rest Day) ersetzt und als gelöscht markiert (Soft Delete), sodass alle ursprünglich generierten Workouts in der Datenbank für Analysezwecke erhalten bleiben.

## Endpoint

```
DELETE /api/v2/workouts/{workoutId}/skip
```

**Authentifizierung:** Erforderlich (Bearer Token)

**HTTP-Methode:** `DELETE` (RESTful - passt semantisch zum "Entfernen" eines Workouts)

**Controller:** `SkipWorkoutController` (Invokable, Separation of Concerns)

## Parameter

### Path Parameter

- `workoutId` (integer, erforderlich): Die ID des zu überspringenden Workouts

## Anfrage

### Headers

```
Authorization: Bearer {token}
Content-Type: application/json
```

### Beispiel

```bash
curl -X DELETE https://api.example.com/api/v2/workouts/123/skip \
  -H "Authorization: Bearer your-token-here"
```

## Antwort

### Erfolgreiche Antwort (200 OK)

```json
{
  "message": "Workout skipped successfully",
  "original_workout": {
    "id": 123,
    "name": "Push Day",
    "type": "strength",
    "date": "2026-01-15",
    "exercises_count": 7
  },
  "rest_day": {
    "id": 123,
    "name": "Active Recovery",
    "date": "2026-01-15",
    "type": "rest",
    "description": "Take a rest day to allow your muscles to recover and grow."
  },
  "skipped_at": "2026-01-12T10:30:45+00:00"
}
```

### Fehlerantworten

#### 403 Forbidden - Nicht autorisiert

```json
{
  "error": "Unauthorized",
  "message": "You do not have access to this workout"
}
```

Dieser Fehler tritt auf, wenn das Workout nicht zum Plan des angemeldeten Benutzers gehört.

#### 404 Not Found - Workout nicht gefunden

```json
{
  "message": "No query results for model [App\\Models\\WorkoutPlan] 123"
}
```

#### 422 Unprocessable Entity - Ungültige Operation

**Fall 1: Workout ist bereits ein Ruhetag**

```json
{
  "error": "Invalid operation",
  "message": "Cannot skip a rest day"
}
```

**Fall 2: Workout wurde bereits übersprungen**

```json
{
  "error": "Invalid operation",
  "message": "This workout has already been skipped"
}
```

## Funktionsweise

### 1. Datenerhaltung

Wenn ein Workout übersprungen wird:

- Das ursprüngliche Workout wird soft deleted (deleted_at timestamp wird gesetzt)
- Ein neuer Rest Day wird mit dem gleichen `date` und `day_number` erstellt
- Die ursprünglichen Übungen bleiben mit dem soft-deleted Workout in der Datenbank erhalten
- Der neue Rest Day erhält eine neue ID

### 2. Datenbankstruktur

**Vor dem Überspringen:**
```
Workout ID: 123
plan_id: 1
day_number: 5
date: "2026-01-15"
workout_name: "Push Day"
workout_type: "strength"
deleted_at: NULL
+ 7 verknüpfte Übungen
```

**Nach dem Überspringen:**

*Soft Deleted Workout:*
```
Workout ID: 123 (SOFT DELETED)
plan_id: 1
day_number: 5
date: "2026-01-15"
workout_name: "Push Day"
workout_type: "strength"
deleted_at: "2026-01-12 10:30:45"
+ 7 verknüpfte Übungen (erhalten)
```

*Neuer Rest Day:*
```
Workout ID: 456 (NEUER REST DAY)
plan_id: 1
day_number: 5
date: "2026-01-15"
workout_name: "Active Recovery"
workout_type: "rest"
deleted_at: NULL
```

### 3. Unique Constraint (MySQL)

Da MySQL keine **partial unique indexes** wie PostgreSQL unterstützt:

**Migration:**
- Entfernt den unique constraint auf `(plan_id, day_number)`
- Fügt einen regulären (non-unique) index für Performance hinzu

**Application-Level Validation:**
- Controller prüft vor dem Erstellen des Rest Days, ob bereits ein aktiver Workout mit gleicher day_number existiert
- Gibt 409 Conflict zurück, falls ein Konflikt besteht
- Verhindert Datenkonsistenz-Probleme

**Alternative für PostgreSQL:**
```sql
CREATE UNIQUE INDEX workout_plans_plan_id_day_number_unique 
ON workout_plans (plan_id, day_number) 
WHERE deleted_at IS NULL
```

### 4. Vorteile

- **Historische Daten:** Ursprüngliches Workout bleibt vollständig erhalten
- **Analytics:** Alle übersprungenen Workouts über `WorkoutPlan::onlyTrashed()` abrufbar
- **Übungen erhalten:** Alle Exercise-Daten bleiben für Analysen zugänglich
- **Klare Trennung:** Aktiver Rest Day ≠ Übersprungenes Workout
- **Saubere API:** Frontend erhält nur aktive Workouts (ohne withTrashed)

## Migrations

### Migration 1: Soft Deletes

```bash
php artisan make:migration add_deleted_at_to_workout_plans_table --table=workout_plans
```

Fügt `deleted_at` Spalte zur `workout_plans` Tabelle hinzu.

### Migration 2: Unique Constraint Anpassung (MySQL)

```bash
php artisan make:migration update_workout_plans_unique_constraint_with_deleted_at --table=workout_plans
```

**Was passiert:**
- Entfernt den unique constraint auf `(plan_id, day_number)`
- Fügt einen regulären index für Performance hinzu
- **Warum:** MySQL unterstützt keine partial unique indexes (WHERE deleted_at IS NULL)

**Lösung:** Application-level validation im Controller

## Model-Änderungen

Das `WorkoutPlan` Model verwendet:

- `SoftDeletes` Trait
- Regulärer index statt unique constraint
- Validation im Controller für Datenintegrität

## Anwendungsfälle

### Mobile App

```typescript
async function skipWorkout(workoutId: number) {
  try {
    const response = await fetch(`/api/v2/workouts/${workoutId}/skip`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
      }
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message);
    }
    
    const data = await response.json();
    console.log('Workout skipped:', data.original_workout.name);
    console.log('Replaced with:', data.rest_day.name);
    
    return data;
  } catch (error) {
    console.error('Failed to skip workout:', error);
    throw error;
  }
}
```

## Sicherheit

- Authentifizierung ist erforderlich
- Benutzer können nur ihre eigenen Workouts überspringen
- Validation verhindert das Überspringen von Ruhetagen
- Validation verhindert doppeltes Überspringen

## Code-Struktur

### Controller

Der `SkipWorkoutController` ist ein **Invokable Controller** (Single Action Controller):

```php
class SkipWorkoutController extends Controller
{
    public function __invoke(Request $request, int $workout): JsonResponse
    {
        // Skip-Logik
    }
}
```

**Vorteile:**
- Fokussiert auf eine einzige Verantwortung
- Konsistent mit `RescheduleWorkoutController`
- Einfach zu testen und zu warten
- Verwendet DELETE HTTP-Methode (RESTful)

## Best Practices

1. **Vor dem Überspringen prüfen:** Zeige dem Benutzer eine Bestätigung an
2. **Feedback geben:** Informiere den Benutzer, dass das Workout durch einen Ruhetag ersetzt wurde
3. **Analytics:** Tracke, wie oft Workouts übersprungen werden
4. **UI-Update:** Aktualisiere die UI sofort nach erfolgreichem Überspringen

## Zukünftige Erweiterungen

Mögliche Erweiterungen:

- **Skip-Grund:** Optionaler Parameter, um den Grund für das Überspringen zu speichern
- **Skip-Limit:** Maximale Anzahl von Skips pro Woche/Monat
- **Statistiken:** Endpoint für Skip-Statistiken des Benutzers
- **Analytics:** Dashboard für übersprungene Workouts mit withTrashed() Queries

