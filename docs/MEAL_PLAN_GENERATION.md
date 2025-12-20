# Meal Plan Generation Feature

## Übersicht

Nach Abschluss des Onboardings wird automatisch ein personalisierter 28-Tage-Meal-Plan über OpenAI generiert.

## Architektur

### Event-Driven Flow

```
OnboardingController 
  → OnboardingCompleted Event 
    → GenerateMealPlan Listener 
      → GenerateUserMealPlan Job
```

### Komponenten

#### 1. Event: `OnboardingCompleted`
- Wird nach erfolgreicher Erstellung von User, Profile und Plan gefeuert
- Enthält: `User` und `Plan` Objekte

#### 2. Listener: `GenerateMealPlan`
- Hört auf `OnboardingCompleted` Event
- Dispatched `GenerateUserMealPlan` Job zur Queue

#### 3. Job: `GenerateUserMealPlan`
- Generiert 28 Tages-Meal-Pläne via OpenAI
- Nutzt Tool Calling für strukturierte Responses
- Speichert Meals in der Datenbank

## Datenbank-Schema

### `meal_plans` Tabelle
- `plan_id` - Foreign Key zu `plans`
- `date` - Datum des Meal Plans
- `day_number` - Tag im 28-Tage-Plan (1-28)
- `status` - pending | generated | failed
- `total_calories`, `total_protein_g`, `total_carbs_g`, `total_fat_g`

### `meals` Tabelle
- `meal_plan_id` - Foreign Key zu `meal_plans`
- `type` - breakfast | lunch | snack | dinner
- `name` - Name des Gerichts
- `description` - Beschreibung
- `calories`, `protein_g`, `carbs_g`, `fat_g`, `fiber_g`, `sugar_g`
- `ingredients` - JSON Array mit Zutaten
- `instructions` - JSON Array mit Zubereitungsschritten
- `prep_time_minutes`, `cook_time_minutes`
- `difficulty` - Easy | Medium | Hard
- `tags`, `allergens` - JSON Arrays

## OpenAI Integration

### Model: `gpt-4o`

### Tool: `create_meal_plan`
Strukturierte Output-Schema für:
- Array von Meals (breakfast, lunch, snack, dinner)
- Nutrition Facts
- Zutaten mit Mengen
- Zubereitungsanweisungen
- Schwierigkeitsgrad, Zeiten, Tags, Allergene

### System Prompt
Personalisiert basierend auf:
- User Profile (Alter, Geschlecht, Gewicht, Höhe)
- Body Goal (muscle_gain, weight_loss, etc.)
- Diet Type (omnivore, vegetarian, vegan, etc.)
- Activity Level
- Nutritional Targets (aus TDEE-Berechnung)

## Konfiguration

### Environment Variables
```env
OPENAI_API_KEY=sk-...
```

### Queue Configuration
Der Job läuft über Laravel's Queue System. Für Development:
```bash
php artisan queue:work
```

Für Production: Supervisor oder Horizon empfohlen.

## Installation

1. **Migrations ausführen:**
```bash
php artisan migrate
```

2. **OpenAI API Key setzen:**
```bash
echo "OPENAI_API_KEY=sk-..." >> .env
```

3. **Queue Worker starten:**
```bash
php artisan queue:work
```

## Testing

### Manuell Event auslösen:
```php
use App\Events\OnboardingCompleted;

$user = User::find(1);
$plan = $user->plans()->first();

OnboardingCompleted::dispatch($user, $plan);
```

### Job Status prüfen:
```bash
php artisan queue:failed  # Fehlgeschlagene Jobs
tail -f storage/logs/laravel.log  # Log Output
```

## Kosten-Überlegungen

- **Model:** GPT-4o
- **Requests:** 28 pro User (ein Request pro Tag)
- **Durchschnittliche Kosten:** ~$0.10-0.20 pro User
- **Rate Limits:** Beachten bei vielen gleichzeitigen Onboardings

### Optimierungen:
- Batch Processing: Mehrere Tage in einem Request
- Caching: Ähnliche Profile wiederverwenden
- Alternative Models: gpt-3.5-turbo für Kosten-Reduktion

## Monitoring

### Wichtige Logs:
- `Generated meal plan for day X` - Erfolgreiche Generierung
- `Failed to generate meal plan for day X` - Fehler bei Generierung

### Metriken zu überwachen:
- Anzahl generierter Meal Plans pro Tag
- Fehlerrate bei OpenAI Requests
- Durchschnittliche Generation-Zeit
- Queue-Länge

## Erweiterungen

### Geplante Features:
- [ ] Meal-Alternativen generieren
- [ ] Einkaufslisten erstellen
- [ ] Rezept-Bilder via DALL-E
- [ ] Mahlzeiten-Feedback & Anpassung
- [ ] Batch-Generation für Performance

