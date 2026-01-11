# Move Workout to Tomorrow

## Übersicht

Diese Funktionalität ermöglicht es Benutzern, ein geplantes Workout auf den nächsten Tag zu verschieben und den aktuellen Tag durch einen Ruhetag zu ersetzen. Dies ist nützlich, wenn Benutzer sich unwohl fühlen, eine Verletzung haben oder einfach einen zusätzlichen Ruhetag benötigen.

## API Endpoint

```
POST /api/v2/workouts/{workout}/move
```

**Authentifizierung**: Erforderlich (Sanctum Token)

**Route Model Binding**: Verwendet Laravel Route Model Binding für automatisches Laden des Workouts

**Optional Parameters**:
- `force` (boolean, default: `false`) - Ersetzt ein existierendes Workout am nächsten Tag

## Funktionsweise

### 1. Validierungen

Die Funktion führt mehrere Validierungen durch:

- ✅ **Workout existiert**: Das Workout muss existieren (404 wenn nicht)
- ✅ **Benutzer-Autorisierung**: Der Benutzer muss Besitzer des Plans sein
- ✅ **Kein Ruhetag**: Ruhetage können nicht verschoben werden
- ✅ **Plan-Dauer**: Das Workout kann nicht über die Plan-Dauer hinaus verschoben werden
- ✅ **Keine Konflikte**: Es darf noch kein Workout für morgen existieren (es sei denn `force: true`)

### 2. Verschiebe-Prozess

Der Prozess ist **atomar** (alles oder nichts) und läuft in einer Datenbank-Transaktion:

1. **Originaldaten speichern**: Alle Workout- und Exercise-Daten werden gespeichert
2. **Force-Replace (optional)**: Wenn `force: true` und morgen existiert bereits ein Workout:
   - Das existierende Workout und seine Exercises werden gelöscht
   - Info über das gelöschte Workout wird in der Response zurückgegeben
3. **Exercises löschen**: Die Exercises des aktuellen Workouts werden gelöscht
4. **Aktuelles Workout konvertieren**: Wird zu einem Ruhetag mit:
   - Name: "Rest Day"
   - Typ: "rest"
   - Dauer: 0 Minuten
   - Kalorien: 0
   - Schwierigkeit: "easy"
   - Beschreibung: Ruhetag-Hinweise
5. **Neues Workout erstellen**: Für morgen mit allen Originaldaten
6. **Exercises verschieben**: Alle Exercises werden zum neuen Workout kopiert

### 3. Datenerhaltung

Alle Daten werden vollständig erhalten:
- ✅ Workout-Name, Typ, Dauer, Kalorien, Schwierigkeit
- ✅ Beschreibung und Muskelgruppen
- ✅ Alle Exercises mit allen Eigenschaften
- ✅ Exercise-Reihenfolge (order)
- ✅ Arrays (instructions, muscle_groups, equipment, alternatives)

## Request

### Basis-Verschiebung (ohne force)

```bash
curl -X POST https://api.example.com/api/v2/workouts/123/move \
  -H "Authorization: Bearer {token}"
```

### Force-Replace (existierendes Workout ersetzen)

```bash
curl -X POST https://api.example.com/api/v2/workouts/123/move \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"force": true}'
```

**Parameter**:
- `force` (optional, boolean): Wenn `true`, wird ein existierendes Workout am nächsten Tag ersetzt

## Response

### Erfolg (200 OK)

#### Ohne Replacement

```json
{
  "message": "Workout moved to tomorrow successfully",
  "rest_day": {
    "id": 123,
    "date": "2026-01-12",
    "name": "Rest Day",
    "type": "rest",
    "description": "Take a rest day to recover. Stay hydrated and focus on mobility."
  },
  "moved_workout": {
    "id": 456,
    "date": "2026-01-13",
    "name": "Push Day",
    "type": "strength",
    "duration_minutes": 60,
    "exercises_count": 5
  }
}
```

#### Mit Force-Replacement

```json
{
  "message": "Workout moved to tomorrow successfully",
  "rest_day": {
    "id": 123,
    "date": "2026-01-12",
    "name": "Rest Day",
    "type": "rest",
    "description": "Take a rest day to recover. Stay hydrated and focus on mobility."
  },
  "moved_workout": {
    "id": 456,
    "date": "2026-01-13",
    "name": "Push Day",
    "type": "strength",
    "duration_minutes": 60,
    "exercises_count": 5
  },
  "replaced_workout": {
    "id": 789,
    "name": "Pull Day",
    "type": "strength",
    "date": "2026-01-13"
  }
}
```

### Fehler-Responses

#### 400 - Ruhetag kann nicht verschoben werden

```json
{
  "error": "Invalid operation",
  "message": "Cannot move a rest day"
}
```

#### 400 - Über Plan-Dauer hinaus

```json
{
  "error": "Invalid operation",
  "message": "Cannot move workout beyond plan duration",
  "plan_end_date": "2026-02-10"
}
```

#### 401 - Nicht authentifiziert

```json
{
  "message": "Unauthenticated."
}
```

#### 403 - Nicht autorisiert

```json
{
  "error": "Unauthorized",
  "message": "You do not have access to this workout"
}
```

#### 404 - Workout nicht gefunden

```json
{
  "error": "Not found",
  "message": "Workout not found"
}
```

#### 409 - Konflikt (Morgen bereits belegt)

```json
{
  "error": "Conflict",
  "message": "Tomorrow already has a workout scheduled. Use force=true to replace it.",
  "tomorrow_workout": {
    "id": 789,
    "name": "Pull Day",
    "type": "strength"
  }
}
```

**Lösung**: Nutze `force: true` Parameter im Request-Body, um das existierende Workout zu ersetzen.

#### 500 - Server-Fehler

```json
{
  "error": "Server error",
  "message": "Failed to move workout. Please try again."
}
```

## Implementierung

### Controller

**File**: `app/Http/Controllers/Api/V2/MoveWorkoutController.php`

Ein **dedizierter Invokable Single-Action Controller** für diese Funktionalität:

```php
class MoveWorkoutController extends Controller
{
    public function __invoke(Request $request, WorkoutPlan $workout): JsonResponse
    {
        // Laravel Route Model Binding lädt automatisch das Workout
        // 404 wird automatisch geworfen wenn nicht gefunden
    }
}
```

**Vorteile**:
- ✅ Single Responsibility Principle
- ✅ Saubere Separation of Concerns
- ✅ Laravel Route Model Binding (automatische 404-Behandlung)
- ✅ Invokable Controller (keine Methodennamen nötig)

Die Methode verwendet:
- Datenbank-Transaktionen für Atomarität
- Model-Eager-Loading für Performance
- Umfassendes Error-Handling
- Logging für Debugging

### Route

**File**: `routes/api.php`

```php
Route::post('/workouts/{workout}/move', MoveWorkoutController::class);
```

**RESTful Design**:
- Ressourcen-orientiert: `/workouts/{workout}/move`
- Verwendung von Route Model Binding
- POST-Methode für Zustandsänderung
- Kurz und prägnant

## Tests

**File**: `tests/Feature/WorkoutMoveToTomorrowTest.php`

Umfassende Test-Suite mit **12 Tests** und **98 Assertions**:

### Test-Szenarien

1. ✅ **Erfolgreiche Verschiebung**: Workout wird verschoben, aktueller Tag wird Ruhetag
2. ✅ **Konflikt-Prävention**: Fehler wenn morgen bereits ein Workout existiert
3. ✅ **Ruhetag-Schutz**: Ruhetage können nicht verschoben werden
4. ✅ **Plan-Dauer-Check**: Workout kann nicht über Plan-Ende verschoben werden
5. ✅ **Benutzer-Isolation**: Benutzer können nur eigene Workouts verschieben
6. ✅ **Authentifizierung**: Unauthentifizierte Anfragen werden abgelehnt
7. ✅ **404 Handling**: Nicht existierende Workouts geben 404 zurück
8. ✅ **Workout-Eigenschaften**: Alle Workout-Eigenschaften werden erhalten
9. ✅ **Exercise-Eigenschaften**: Alle Exercise-Eigenschaften inkl. Arrays werden erhalten
10. ✅ **Exercise-Reihenfolge**: Die Reihenfolge bleibt erhalten
11. ✅ **Ruhetag-Eigenschaften**: Der konvertierte Ruhetag hat korrekte Werte
12. ✅ **Atomarität**: Bei Fehlern wird alles zurückgerollt (all-or-nothing)

### Tests ausführen

```bash
php artisan test --filter=WorkoutMoveToTomorrowTest
# oder
./vendor/bin/pest --filter=WorkoutMoveToTomorrowTest
```

**Ergebnis**: ✅ 12 passed (98 assertions)

## Use Cases

### 1. Krankheit/Verletzung

Benutzer fühlt sich krank und möchte heute pausieren:
```
Heute: "Leg Day" → wird zu "Rest Day"
Morgen: Neu erstellt "Leg Day"
```

### 2. Zeitmanagement

Benutzer hat heute keine Zeit für das Workout:
```
Heute: "Full Body" → wird zu "Rest Day"
Morgen: Neu erstellt "Full Body"
```

### 3. Übertraining-Prävention

Benutzer fühlt sich erschöpft und braucht mehr Erholung:
```
Heute: "HIIT Cardio" → wird zu "Rest Day"
Morgen: Neu erstellt "HIIT Cardio"
```

## Technische Details

### Datenbank-Transaktion

```php
DB::transaction(function () {
    // 1. Originaldaten speichern
    // 2. Exercises löschen
    // 3. Aktuelles Workout zu Ruhetag konvertieren
    // 4. Neues Workout für morgen erstellen
    // 5. Exercises kopieren
});
```

Wenn irgendein Schritt fehlschlägt, wird die gesamte Transaktion zurückgerollt.

### Logging

Erfolgreiche Verschiebungen und Fehler werden geloggt:

```php
Log::info('Workout moved to tomorrow', [
    'user_id' => $user->id,
    'original_workout_id' => $workout->id,
    'new_workout_id' => $newWorkout->id,
    'original_date' => $workout->date->format('Y-m-d'),
    'new_date' => $newWorkout->date->format('Y-m-d'),
]);
```

### Unique Constraints

Die Migration hat einen Unique Constraint auf `(plan_id, day_number)`:
- Dies verhindert Duplikate
- Die Verschiebung prüft vorher, ob `day_number + 1` bereits existiert

## Zukünftige Erweiterungen

Mögliche Erweiterungen:

1. **Flexibles Verschieben**: Auf beliebiges Datum statt nur "morgen"
2. **Workout tauschen**: Zwei Workouts tauschen statt nur verschieben
3. **Plan neu-anordnen**: Alle nachfolgenden Workouts um einen Tag verschieben
4. **Historie**: Verschiebungs-Historie für Analytics
5. **Benachrichtigungen**: Push-Notification über die Verschiebung

## Wichtige Hinweise

⚠️ **Einschränkungen**:
- Kann nur auf "morgen" verschoben werden (day_number + 1)
- Ruhetage können nicht verschoben werden
- Morgen darf noch kein Workout haben
- Darf nicht über Plan-Ende hinausgehen

✅ **Vorteile**:
- Atomar (alles oder nichts)
- Vollständige Datenerhaltung
- Umfassende Validierung
- Gut getestet


