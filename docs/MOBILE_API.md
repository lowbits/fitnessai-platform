# Mobile App API Endpoints

## Übersicht

Die folgenden API-Endpoints stehen für die Mobile App zur Verfügung. Sie laden echte Daten aus der Datenbank, die von OpenAI generiert wurden.

## Endpoints

### 1. GET `/api/v2/me`

Gibt User-Informationen, aktuellen Plan und Onboarding-Status zurück.

**Response:**
```json
{
  "user": {
    "id": 1,
    "email": "user@example.com",
    "name": "Max Mustermann",
    "avatar_url": null,
    "created_at": "2025-12-20T10:00:00Z",
    "email_verified_at": "2025-12-20T10:00:00Z"
  },
  "current_plan": {
    "id": 1,
    "created_at": "2025-12-20T10:00:00Z",
    "start_date": "2025-12-20",
    "current_day": 1,
    "total_days": 28,
    "goal": "muscle_gain",
    "diet_type": "omnivore",
    "fitness_level": "intermediate",
    "nutrition_targets": {
      "daily_calories": 2800,
      "protein_g": 175,
      "carbs_g": 315,
      "fat_g": 93
    }
  },
  "subscription": {
    "status": "free",
    "tier": "free",
    "features": {
      "full_plan_access": false,
      "max_days_accessible": 4
    }
  },
  "stats": {
    "days_completed": 0,
    "workouts_completed": 0,
    "meals_logged": 0,
    "streak": 0
  },
  "onboarding": {
    "completed": true,
    "steps": {
      "questionnaire": true,
      "plan_generated": true
    }
  }
}
```

---

### 2. GET `/api/v2/plan/day/{date}`

Gibt den Meal Plan für einen bestimmten Tag zurück.

**Parameter:**
- `date` (string): Datum im Format `YYYY-MM-DD` (z.B. `2025-12-20`)

**Response (Success):**
```json
{
  "plan_id": 1,
  "plan_day": 1,
  "total_days": 28,
  "date": "2025-12-20",
  "day_name": "Friday",
  "locked": false,
  "status": "generated",
  "meals": [
    {
      "id": 1,
      "name": "Protein Oatmeal mit Beeren",
      "type": "Breakfast",
      "image": "breakfast",
      "calories": 520,
      "protein_g": 35,
      "carbs_g": 65,
      "fat_g": 12
    },
    {
      "id": 2,
      "name": "Gegrillte Hähnchenbrust mit Quinoa",
      "type": "Lunch",
      "image": "lunch",
      "calories": 650,
      "protein_g": 55,
      "carbs_g": 70,
      "fat_g": 18
    },
    {
      "id": 3,
      "name": "Griechischer Joghurt mit Nüssen",
      "type": "Snack",
      "image": "snack",
      "calories": 180,
      "protein_g": 15,
      "carbs_g": 20,
      "fat_g": 8
    },
    {
      "id": 4,
      "name": "Lachs mit Süßkartoffel",
      "type": "Dinner",
      "image": "dinner",
      "calories": 680,
      "protein_g": 48,
      "carbs_g": 75,
      "fat_g": 22
    }
  ],
  "workout": null,
  "daily_totals": {
    "calories": 2030,
    "protein_g": 153,
    "carbs_g": 230,
    "fat_g": 60
  },
  "stats": {
    "days_completed": 0,
    "workouts_completed": 0,
    "meals_logged": 0,
    "streak": 0
  }
}
```

**Response (Locked - Premium Required):**
```json
{
  "locked": true,
  "day_number": 5,
  "date": "2025-12-24",
  "unlock_required": {
    "title": "🔒 Day 5 Locked",
    "message": "Upgrade to Premium to unlock all 28 days",
    "price": "€3.99/month",
    "benefits": [
      "Access to all 28 days",
      "Unlimited plan regeneration",
      "Meal alternatives",
      "Exercise substitutions"
    ],
    "cta": "Unlock Premium"
  }
}
```

**Response (Generating):**
```json
{
  "plan_id": 1,
  "plan_day": 1,
  "status": "generating",
  "message": "Your meal plan is being generated. This may take a few moments.",
  "meals": [],
  "workout": null
}
```

**Response (Not Generated Yet):**
```json
{
  "plan_id": 1,
  "plan_day": 1,
  "status": "not_generated",
  "message": "Meal plan for this day has not been generated yet. Please check back soon.",
  "meals": [],
  "workout": null
}
```

**Response (Failed):**
```json
{
  "plan_id": 1,
  "plan_day": 1,
  "status": "failed",
  "message": "Failed to generate meal plan. Please contact support.",
  "meals": [],
  "workout": null
}
```

---

### 3. GET `/api/v2/meals/{mealId}`

Gibt detaillierte Informationen zu einer spezifischen Mahlzeit zurück.

**Parameter:**
- `mealId` (integer): ID der Mahlzeit

**Response:**
```json
{
  "id": 1,
  "name": "Protein Oatmeal mit Beeren",
  "type": "Breakfast",
  "image": "breakfast",
  "description": "Protein-reiches Haferflocken-Frühstück mit frischen Beeren und Nüssen.",
  "nutrition": {
    "calories": 520,
    "protein_g": 35,
    "carbs_g": 65,
    "fat_g": 12,
    "fiber_g": 10,
    "sugar_g": 15
  },
  "ingredients": [
    {
      "name": "Haferflocken",
      "amount": "80",
      "unit": "g"
    },
    {
      "name": "Proteinpulver (Vanille)",
      "amount": "30",
      "unit": "g"
    },
    {
      "name": "Mandelmilch",
      "amount": "250",
      "unit": "ml"
    },
    {
      "name": "Blaubeeren",
      "amount": "100",
      "unit": "g"
    },
    {
      "name": "Walnüsse",
      "amount": "20",
      "unit": "g"
    }
  ],
  "instructions": [
    "Haferflocken mit Mandelmilch in einem Topf erhitzen.",
    "Bei mittlerer Hitze 5 Minuten köcheln lassen und dabei umrühren.",
    "Proteinpulver einrühren.",
    "Mit Blaubeeren und gehackten Walnüssen garnieren.",
    "Warm servieren."
  ],
  "prep_time_minutes": 5,
  "cook_time_minutes": 5,
  "total_time_minutes": 10,
  "difficulty": "Easy",
  "servings": 1,
  "tags": ["High-Protein", "Quick", "Vegetarian"],
  "allergens": ["Nüsse", "Milch"]
}
```

---

## Status-Felder Erklärung

### Meal Plan Status
- **`not_generated`**: Meal Plan wurde noch nicht erstellt
- **`pending`**: Meal Plan wird gerade generiert
- **`generating`**: Alias für `pending` (user-friendly)
- **`generated`**: Meal Plan erfolgreich generiert
- **`failed`**: Generierung fehlgeschlagen

### Locked Status
- **`locked: false`**: Tag ist zugänglich
- **`locked: true`**: Tag ist gesperrt (Premium erforderlich)
- Free Users: Zugriff auf Tage 1-4
- Premium Users: Zugriff auf alle 28 Tage

---

## Fehlerbehandlung

### 404 Not Found
```json
{
  "error": "No user found in database"
}
```

```json
{
  "error": "No active plan found"
}
```

```json
{
  "error": "Meal not found"
}
```

### 403 Forbidden
Wird zurückgegeben, wenn ein Tag gesperrt ist (siehe Locked Response oben).

### 500 Internal Server Error
Wird zurückgegeben, wenn die Meal Plan Generierung fehlgeschlagen ist.

---

## Mobile App Integration

### Empfohlener Flow

1. **App Start:**
   - `GET /api/v2/me` → User-Daten und aktuellen Plan laden
   - Onboarding-Status prüfen

2. **Dashboard:**
   - Aktuellen Tag berechnen basierend auf `start_date` und `current_day`
   - `GET /api/v2/plan/day/{date}` → Tagesplan laden

3. **Meal Details:**
   - User klickt auf Mahlzeit
   - `GET /api/v2/meals/{mealId}` → Detaillierte Rezept-Infos

4. **Loading States:**
   - `status: "generating"` → Lade-Spinner zeigen
   - `status: "not_generated"` → "Wird erstellt" Nachricht
   - `status: "failed"` → Fehlermeldung + Support-Link

5. **Premium Upsell:**
   - `locked: true` → Premium-Paywall anzeigen
   - CTA und Benefits aus `unlock_required` nutzen

### Polling für Status-Updates

Wenn `status: "generating"`:
```javascript
const pollMealPlan = async (date) => {
  const response = await fetch(`/api/v2/plan/day/${date}`);
  const data = await response.json();
  
  if (data.status === 'generating' || data.status === 'pending') {
    // Warte 5 Sekunden und versuche erneut
    setTimeout(() => pollMealPlan(date), 5000);
  } else {
    // Status ist 'generated' oder 'failed'
    updateUI(data);
  }
};
```

---

## Testing

### Mit cURL:

```bash
# User Info
curl http://localhost:8000/api/v2/me

# Tagesplan
curl http://localhost:8000/api/v2/plan/day/2025-12-20

# Meal Details
curl http://localhost:8000/api/v2/meals/1
```

### Mit Postman/Insomnia:

Importiere die Endpoints und teste verschiedene Szenarien:
- Freie Tage (1-4)
- Gesperrte Tage (5+)
- Verschiedene Status (generating, generated, failed)

---

## Nächste Schritte

### Authentication
Aktuell wird immer der erste User aus der Datenbank geladen. Implementiere:
- Laravel Sanctum Token-basierte Auth
- `auth()->user()` statt `User::first()`

### Workout-Daten
Wenn Workout-Generierung implementiert ist:
- Workout-Details zum `/plan/day` Response hinzufügen
- Separater Endpoint für Workout-Details

### Zusätzliche Endpoints (geplant):
- `POST /api/v2/meals/{id}/log` - Mahlzeit als gegessen markieren
- `POST /api/v2/workouts/{id}/complete` - Workout als abgeschlossen markieren
- `GET /api/v2/progress` - Fortschritts-Tracking
- `POST /api/v2/plan/regenerate` - Plan neu generieren (Premium)

