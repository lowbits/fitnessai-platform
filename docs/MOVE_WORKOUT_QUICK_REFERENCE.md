# Move Workout - Quick Reference

## 🚀 API Endpoint

```
POST /api/v2/workouts/{workout}/move
```

**Optional Parameters**:
- `force` (boolean, default: `false`) - Replace existing tomorrow workout if it exists

## 📝 Beispiel-Requests

### cURL

```bash
# Basic move (fails if tomorrow has workout)
curl -X POST https://api.example.com/api/v2/workouts/123/move \
  -H "Authorization: Bearer YOUR_TOKEN"

# Force replace existing tomorrow workout
curl -X POST https://api.example.com/api/v2/workouts/123/move \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"force": true}'
```

### JavaScript/TypeScript

```typescript
// Basic move
const response = await fetch(`${API_BASE_URL}/api/v2/workouts/${workoutId}/move`, {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
  },
});

// Force replace
const response = await fetch(`${API_BASE_URL}/api/v2/workouts/${workoutId}/move`, {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({ force: true }),
});
```

### PHP (Guzzle)

```php
// Basic move
$response = $client->post("/api/v2/workouts/{$workoutId}/move", [
    'headers' => [
        'Authorization' => "Bearer {$token}",
    ],
]);

// Force replace
$response = $client->post("/api/v2/workouts/{$workoutId}/move", [
    'headers' => [
        'Authorization' => "Bearer {$token}",
    ],
    'json' => [
        'force' => true,
    ],
]);
```

## ✅ Success Response (200)

### Without Replacement

```json
{
  "message": "Workout moved to tomorrow successfully",
  "rest_day": {
    "id": 123,
    "date": "2026-01-12",
    "name": "Rest Day",
    "type": "rest",
    "description": "Take a rest day to recover..."
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

### With Force Replacement

```json
{
  "message": "Workout moved to tomorrow successfully",
  "rest_day": {
    "id": 123,
    "date": "2026-01-12",
    "name": "Rest Day",
    "type": "rest",
    "description": "Take a rest day to recover..."
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

## ❌ Error Responses

| Code | Error | Bedeutung |
|------|-------|-----------|
| 400 | Invalid operation | Ruhetag oder über Plan-Ende |
| 401 | Unauthenticated | Token fehlt/ungültig |
| 403 | Unauthorized | Nicht dein Workout |
| 404 | Not found | Workout existiert nicht |
| 409 | Conflict | Morgen bereits belegt |
| 500 | Server error | Interner Fehler |

## 🔍 Error-Details

### 400 - Ruhetag

```json
{
  "error": "Invalid operation",
  "message": "Cannot move a rest day"
}
```

### 400 - Plan-Ende

```json
{
  "error": "Invalid operation",
  "message": "Cannot move workout beyond plan duration",
  "plan_end_date": "2026-02-10"
}
```

### 409 - Konflikt

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

**Lösung**: Nutze `force: true` Parameter um das bestehende Workout zu ersetzen.

## 🏗️ Implementierung

### Controller
- **File**: `app/Http/Controllers/Api/V2/MoveWorkoutController.php`
- **Type**: Invokable Single-Action Controller
- **Method**: `__invoke(Request $request, WorkoutPlan $workout)`

### Route
```php
Route::post('/workouts/{workout}/move', MoveWorkoutController::class);
```

### Features
- ✅ Route Model Binding (automatische 404-Behandlung)
- ✅ Database Transactions (atomare Operation)
- ✅ Vollständige Datenerhaltung
- ✅ Umfassendes Error-Handling
- ✅ Logging

## 🧪 Tests

**File**: `tests/Feature/WorkoutMoveToTomorrowTest.php`

```bash
php artisan test --filter=WorkoutMoveToTomorrowTest
```

**Ergebnis**: ✅ 19 passed (128 assertions)

**Abgedeckt**:
- ✅ Basis-Verschiebung ohne Konflikt
- ✅ Force-Replace mit `force: true`
- ✅ Conflict-Fehler ohne `force`
- ✅ Verschiedene force-Werte (true, "true", 1)
- ✅ Atomarität bei Force-Replace
- ✅ Response enthält replaced_workout Info

## 🎯 Use Cases

| Szenario | Action |
|----------|--------|
| 😷 Krank | Workout heute pausieren |
| ⏰ Keine Zeit | Auf morgen verschieben |
| 💪 Übertraining | Zusätzlicher Ruhetag |
| 🔄 Spontane Änderung | Flexibel umplanen |

## ⚡ Quick Tips

### Vor dem Request prüfen:
- ✅ User ist authentifiziert
- ✅ Workout gehört dem User
- ✅ Workout ist kein Ruhetag
- ✅ Morgen ist noch frei
- ✅ Innerhalb der Plan-Dauer

### Nach dem Request:
- ✅ Status Code prüfen
- ✅ Error Messages dem User zeigen
- ✅ UI aktualisieren (Plan neu laden)
- ✅ Optional: Success-Toast zeigen

## 📱 Mobile Integration

### React Native Beispiel

```typescript
const handleMoveWorkout = async () => {
  try {
    const result = await moveWorkoutToTomorrow(workoutId, token);
    Alert.alert('Erfolg!', result.message);
    // Refresh plan
  } catch (error) {
    Alert.alert('Fehler', error.message);
  }
};
```

### Flutter Beispiel

```dart
Future<void> moveWorkout(int workoutId) async {
  final response = await http.post(
    Uri.parse('$apiUrl/api/v2/workouts/$workoutId/move'),
    headers: {'Authorization': 'Bearer $token'},
  );
  
  if (response.statusCode == 200) {
    // Success
  } else {
    // Handle error
  }
}
```

## 📚 Weitere Docs

- [Vollständige API-Dokumentation](MOVE_WORKOUT_TO_TOMORROW.md)
- [Mobile Integration Guide](MOVE_WORKOUT_TO_TOMORROW_MOBILE.md)
- [Implementation Summary](MOVE_WORKOUT_TO_TOMORROW_SUMMARY.md)

## 🔗 Related Endpoints

```
GET  /api/v2/workouts/{workoutId}     - Workout Details
GET  /api/v2/plan/day/{date}          - Tagesplan
POST /api/v2/track/workouts           - Workout tracken
```


