# Tracked Calories in Day Plan

## Feature Übersicht

Wenn ein User seinen Tagesplan abruft (`GET /api/v2/plan/day/{date}`), erhält er jetzt auch alle getrackten Kalorien für diesen Tag.

## Wichtige Änderungen

### Factory-Anpassungen
Die `MealPlanFactory` und `WorkoutPlanFactory` wurden angepasst, um standardmäßig den Status `'generated'` zu verwenden statt zufällige Werte (`pending`, `generated`, `failed`). Dies stellt sicher, dass Tests konsistent funktionieren und die erwartete JSON-Struktur zurückgeben.

**Vor:**
```php
'status' => fake()->randomElement(['pending', 'generated', 'failed']),
```

**Nach:**
```php
'status' => 'generated',
```

Dies ist wichtig, weil `pending` und `failed` Status unterschiedliche Response-Strukturen zurückgeben.

## API Response Struktur

```json
{
  "plan_id": 1,
  "plan_day": 6,
  "total_days": 90,
  "date": "2026-01-04",
  "day_name": "Saturday",
  "locked": false,
  "status": "generated",
  "meals": [
    {
      "id": 1,
      "name": "Breakfast Bowl",
      "type": "Breakfast",
      "image": "breakfast_placeholder",
      "calories": 500,
      "protein_g": 30,
      "carbs_g": 60,
      "fat_g": 15,
      "is_completed": true
    },
    {
      "id": 2,
      "name": "Grilled Chicken Salad",
      "type": "Lunch",
      "image": "lunch_placeholder",
      "calories": 700,
      "protein_g": 50,
      "carbs_g": 40,
      "fat_g": 25,
      "is_completed": false
    }
  ],
  "workout": {...},
  "daily_totals": {
    "calories": 2000,
    "protein_g": 150,
    "carbs_g": 200,
    "fat_g": 60
  },
  "tracked_calories": {
    "entries": [
      {
        "id": 1,
        "meal_id": 5,
        "meal_name": "Breakfast Bowl",
        "meal_type": "breakfast",
        "calories": 450.0,
        "protein_g": 25.0,
        "carbs_g": 50.0,
        "fat_g": 15.0,
        "notes": "Tasted great!",
        "tracked_at": "2026-01-04T08:30:00.000000Z"
      },
      {
        "id": 2,
        "meal_id": null,
        "meal_name": "Protein Shake",
        "meal_type": null,
        "calories": 300.0,
        "protein_g": 30.0,
        "carbs_g": 10.0,
        "fat_g": 5.0,
        "notes": null,
        "tracked_at": "2026-01-04T10:15:00.000000Z"
      }
    ],
    "totals": {
      "calories": 750.0,
      "protein_g": 55.0,
      "carbs_g": 60.0,
      "fat_g": 20.0
    },
    "count": 2
  },
  "message": null
}
```

## Vorteile

✅ **Automatische Aggregation**: Alle Kalorien-Einträge des Tages werden automatisch summiert
✅ **Detaillierte Einträge**: Jeder einzelne Tracking-Eintrag ist sichtbar mit allen Details
✅ **Meal-Referenz**: Falls der User eine Mahlzeit aus seinem Plan getrackt hat, wird diese referenziert
✅ **Custom Einträge**: User können auch Mahlzeiten tracken, die nicht im Plan sind
✅ **Persistenz**: Tracking bleibt erhalten, auch wenn die Mahlzeit aus dem Plan gelöscht wird
✅ **is_completed Flag**: Jede Mahlzeit im Plan zeigt an, ob sie heute bereits getrackt wurde

## Meal Completion Status

### Wie es funktioniert:

Wenn ein User eine Mahlzeit aus seinem Plan trackt, wird automatisch das `completed_at` Feld auf dem Meal-Model aktualisiert:

1. **Beim Tracking erstellen** (`POST /api/v2/track/calories` mit `meal_id`):
   - Das `completed_at` Feld wird auf `now()` gesetzt
   
2. **Beim Tracking löschen** (`DELETE /api/v2/track/calories/{id}`):
   - Das `completed_at` Feld wird auf `null` zurückgesetzt

3. **Im Tagesplan**:
   - `is_completed`: Boolean (true wenn `completed_at` nicht null ist)
   - `completed_at`: ISO String Timestamp oder null

### Vorteile dieses Ansatzes:

✅ **Persistenz**: Der Completion-Status wird direkt auf dem Meal gespeichert
✅ **Performance**: Keine zusätzliche Query nötig beim Abrufen des Tagesplans
✅ **Einfachheit**: Logik ist klar und direkt im Controller
✅ **Zeitstempel**: Zeigt nicht nur OB, sondern auch WANN eine Mahlzeit getrackt wurde

## Vergleich mit Tages-Plan

User sehen:
- **daily_totals**: Was laut Plan gegessen werden sollte
- **tracked_calories**: Was tatsächlich getrackt/gegessen wurde

So können sie einfach vergleichen: Soll vs. Ist!

## Beispiel Use Cases

1. User trackt Mahlzeiten aus seinem Plan → meal_id ist gesetzt
2. User trackt zusätzliche Snacks → meal_id ist null, meal_name wird gesetzt
3. User trackt nur Kalorien ohne Details → nur calories Feld ist gefüllt
4. User will Tages-Fortschritt sehen → totals zeigt Gesamtsumme

