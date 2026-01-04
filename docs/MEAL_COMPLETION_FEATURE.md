# Meal Completion Feature

## Übersicht

Mahlzeiten können als "completed" markiert werden, entweder durch:
1. Tracking der Mahlzeit mit Kalorien (`POST /api/v2/track/calories` mit `meal_id`)
2. Direktes Abhaken ohne Tracking (`POST /api/v2/meals/{meal}/complete`)

## Model Helper-Methoden

### Meal Model

```php
// Mark meal as completed
$meal->markAsCompleted();

// Mark meal as incomplete
$meal->markAsIncomplete();

// Check if meal is completed
$meal->isCompleted(); // returns bool
```

## API Endpoints

### Mark Meal as Completed
```http
POST /api/v2/meals/{meal}/complete
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Meal marked as completed",
  "meal": {
    "id": 1,
    "name": "Breakfast Bowl",
    "is_completed": true,
    "completed_at": "2026-01-04T08:30:00.000000Z"
  }
}
```

### Mark Meal as Incomplete
```http
DELETE /api/v2/meals/{meal}/complete
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Meal marked as incomplete",
  "meal": {
    "id": 1,
    "name": "Breakfast Bowl",
    "is_completed": false,
    "completed_at": null
  }
}
```

## Use Cases

### 1. Tracking mit Kalorien (automatisch completed)
```javascript
// User trackt eine Mahlzeit mit Details
POST /api/v2/track/calories
{
  "meal_id": 1,
  "tracked_date": "2026-01-04",
  "calories": 500,
  "protein_g": 30,
  "carbs_g": 60,
  "fat_g": 15
}

// Meal wird automatisch als completed markiert
// meal->markAsCompleted() wird intern aufgerufen
```

### 2. Schnelles Abhaken (nur completed, kein Tracking)
```javascript
// User hakt Mahlzeit ab, ohne Details zu tracken
POST /api/v2/meals/1/complete

// Nur completed_at wird gesetzt
// Kein CalorieTracking-Eintrag wird erstellt
```

### 3. Abhaken rückgängig machen
```javascript
// User möchte das Häkchen entfernen
DELETE /api/v2/meals/1/complete

// completed_at wird auf null gesetzt
```

### 4. Tracking löschen (automatisch uncompleted)
```javascript
// User löscht sein Calorie Tracking
DELETE /api/v2/track/calories/123

// Meal wird automatisch als incomplete markiert
// meal->markAsIncomplete() wird intern aufgerufen
```

## Workflow-Beispiele

### Szenario 1: Nur abhaken
```
1. User sieht Mahlzeit im Plan
2. User klickt "Erledigt" → POST /api/v2/meals/{id}/complete
3. Meal zeigt is_completed: true
4. KEIN CalorieTracking-Eintrag
```

### Szenario 2: Tracken mit Details
```
1. User sieht Mahlzeit im Plan
2. User klickt "Track" und gibt Details ein
3. POST /api/v2/track/calories mit meal_id
4. meal->markAsCompleted() wird automatisch aufgerufen
5. Meal zeigt is_completed: true
6. CalorieTracking-Eintrag existiert
```

### Szenario 3: Von "abgehakt" zu "getrackt"
```
1. Meal ist completed (nur abgehakt)
2. User entscheidet sich, doch Details zu tracken
3. POST /api/v2/track/calories mit meal_id
4. completed_at bleibt (wird nur aktualisiert)
5. Jetzt gibt es auch einen CalorieTracking-Eintrag
```

## Vorteile

✅ **Flexibilität**: User können wählen zwischen schnellem Abhaken oder detailliertem Tracking
✅ **Konsistenz**: completed_at wird automatisch synchronisiert
✅ **Sauber**: Helper-Methoden im Model, wiederverwendbar
✅ **DRY**: Eine zentrale Stelle für die Logik
✅ **Einfach**: `$meal->markAsCompleted()` ist selbsterklärend

## Implementierung Details

### In CalorieTrackingController
```php
// Store
if ($tracking->meal_id && $tracking->meal) {
    $tracking->meal->markAsCompleted();
}

// Destroy
if ($calorieTracking->meal_id && $calorieTracking->meal) {
    $calorieTracking->meal->markAsIncomplete();
}
```

### In MealController
```php
// Complete
$meal->markAsCompleted();

// Incomplete
$meal->markAsIncomplete();
```

## Tests

- ✅ `meal can be marked as completed`
- ✅ `meal can be marked as incomplete`
- ✅ `user can mark meal as completed via API`
- ✅ `user can mark meal as incomplete via API`
- ✅ `user cannot mark another users meal as completed`
- ✅ `marking meal as completed via API requires authentication`
- ✅ `tracking a meal automatically sets completed_at`
- ✅ `deleting tracking resets completed_at`

