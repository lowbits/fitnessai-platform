# Workout Plan Generation Feature

## Übersicht

Nach Abschluss des Onboardings werden automatisch personalisierte 28-Tage Workout-Pläne über OpenAI generiert, parallel zu den Meal Plans.

## Architektur

### Event-Driven Flow

```
OnboardingController 
  → OnboardingCompleted Event 
    → GenerateWorkoutPlan Listener 
      → GenerateUserWorkoutPlan Job
```

### Komponenten

#### 1. Event: `OnboardingCompleted`
- Wird nach erfolgreicher Erstellung von User, Profile und Plan gefeuert
- Triggert sowohl Meal-Plan als auch Workout-Plan Generierung

#### 2. Listener: `GenerateWorkoutPlan`
- Hört auf `OnboardingCompleted` Event
- Dispatched `GenerateUserWorkoutPlan` Job zur Queue

#### 3. Job: `GenerateUserWorkoutPlan`
- Generiert 28 Tages-Workout-Pläne via OpenAI
- Nutzt Tool Calling für strukturierte Responses
- Handhabt automatisch Rest Days basierend auf `workouts_per_week`
- Speichert Workouts und Exercises in der Datenbank

## Datenbank-Schema

### `workout_plans` Tabelle
- `plan_id` - Foreign Key zu `plans`
- `date` - Datum des Workouts
- `day_number` - Tag im 28-Tage-Plan (1-28)
- `status` - pending | generated | failed
- `workout_name` - Name des Workouts (z.B. "Upper Body Strength")
- `workout_type` - strength | cardio | hiit | rest | mobility
- `estimated_duration_minutes` - Geschätzte Dauer
- `estimated_calories_burned` - Geschätzte verbrannte Kalorien
- `difficulty` - Beginner | Intermediate | Advanced
- `description` - Workout-Beschreibung
- `muscle_groups` - JSON Array mit Muskelgruppen

### `exercises` Tabelle
- `workout_plan_id` - Foreign Key zu `workout_plans`
- `order` - Reihenfolge im Workout (1, 2, 3...)
- `name` - Name der Übung
- `type` - strength | cardio | warmup | cooldown | stretch
- `description` - Übungsbeschreibung
- `video_url` - Link zu Übungsvideo (optional)
- `image` - Bild-URL (optional)
- `sets` - Anzahl Sätze
- `reps` - Anzahl Wiederholungen
- `duration_seconds` - Dauer bei zeitbasierten Übungen
- `rest_seconds` - Pausenzeit zwischen Sätzen
- `tempo` - Tempo-Empfehlung (z.B. "3-0-1-0")
- `weight_recommendation` - Gewichtsempfehlung (z.B. "60-70% 1RM")
- `muscle_groups` - JSON Array mit Muskelgruppen
- `equipment` - JSON Array mit benötigtem Equipment
- `form_cues` - Ausführungshinweise
- `alternatives` - JSON Array mit Alternativübungen
- `difficulty` - Schwierigkeitsgrad

## OpenAI Integration

### Model: `gpt-4o`

### Tool: `create_workout_plan`
Strukturierte Output-Schema für:
- Workout Metadata (Name, Type, Duration, Difficulty)
- Array von Exercises
- Für jede Übung:
  - Sets, Reps, Duration
  - Rest periods, Tempo
  - Weight recommendations
  - Muscle groups, Equipment
  - Form cues, Alternatives

### System Prompt
Personalisiert basierend auf:
- User Profile (Alter, Geschlecht, Body Goal)
- Skill Level (Beginner, Intermediate, Advanced)
- Training Place (Gym, Home, Outdoor)
- Activity Level
- Workouts per Week
- Day Context (für Muscle Split)

## Rest Day Logik

### Automatische Rest Days

Rest Days werden automatisch basierend auf `workouts_per_week` verteilt:

- **1x/week**: Montag
- **2x/week**: Montag, Donnerstag
- **3x/week**: Montag, Mittwoch, Freitag
- **4x/week**: Montag, Mittwoch, Freitag, Sonntag
- **5x/week**: Montag-Mittwoch, Freitag-Samstag
- **6x/week**: Montag-Samstag
- **7x/week**: Jeden Tag

Rest Days werden **ohne OpenAI** generiert:
```php
[
    'workout_name' => 'Active Recovery / Rest Day',
    'workout_type' => 'rest',
    'description' => 'Take a rest day to allow your muscles to recover...',
    'estimated_duration_minutes' => 0,
]
```

## Workout-Typen nach Ziel

### Muscle Gain
- Fokus auf Compound Movements
- 3-4 Sets, 8-12 Reps
- Moderate Rest (60-90s)
- Progressive Overload

### Weight Loss
- Mix aus Strength und Cardio
- Circuit-Style
- Höhere Reps (12-15)
- Kürzere Pausen (30-45s)

### Strength
- Schwere Compounds
- 3-5 Sets, 3-6 Reps
- Längere Pausen (2-3min)
- Niedriger Volumen

### Endurance
- Höheres Volumen
- Leichtere Gewichte
- 12-20 Reps
- Kürzere Pausen

## Training Place Anpassung

### Gym
- Alle Geräte verfügbar
- Barbell, Dumbbell, Cable, Machines
- Optimale Progression

### Home
- Bodyweight fokussiert
- Minimal Equipment (Dumbbells, Resistance Bands)
- Creative Alternatives

### Outdoor
- Bodyweight dominiert
- Park Equipment (Pull-up Bars)
- Cardio-freundlich

## API Integration

### 1. GET `/api/v2/plan/day/{date}`

**Response includes Workout:**
```json
{
  "meals": [...],
  "workout": {
    "id": 1,
    "name": "Upper Body Strength",
    "type": "strength",
    "description": "Focus on chest, shoulders, and triceps...",
    "duration_minutes": 45,
    "exercises_count": 8,
    "difficulty": "Intermediate",
    "muscle_groups": ["Chest", "Shoulders", "Triceps"],
    "status": "generated"
  }
}
```

**Rest Day:**
```json
{
  "workout": {
    "id": 2,
    "name": "Active Recovery / Rest Day",
    "type": "rest",
    "description": "Take a rest day to allow your muscles to recover...",
    "duration_minutes": 0,
    "status": "generated"
  }
}
```

**Generating:**
```json
{
  "workout": {
    "status": "generating",
    "message": "Workout is being generated..."
  }
}
```

### 2. GET `/api/v2/workouts/{workoutId}`

**Response:**
```json
{
  "id": 1,
  "name": "Upper Body Strength",
  "type": "strength",
  "description": "Focus on chest, shoulders, and triceps...",
  "estimated_duration_minutes": 45,
  "estimated_calories_burned": 350,
  "difficulty": "Intermediate",
  "muscle_groups": ["Chest", "Shoulders", "Triceps"],
  "exercises_count": 8,
  "exercises": [
    {
      "id": 1,
      "order": 1,
      "name": "Bankdrücken (Barbell Bench Press)",
      "type": "strength",
      "description": "Compound chest exercise...",
      "sets": 4,
      "reps": 8,
      "rest_seconds": "90",
      "tempo": "3-0-1-0",
      "weight_recommendation": "70-80% 1RM",
      "muscle_groups": ["Chest", "Shoulders", "Triceps"],
      "equipment": ["Barbell", "Bench"],
      "form_cues": "Keep shoulder blades retracted, feet flat on floor...",
      "alternatives": ["Dumbbell Bench Press", "Push-ups"],
      "difficulty": "Intermediate",
      "video_url": null,
      "image": null
    },
    // ... weitere Übungen
  ]
}
```

## Mobile App Integration

### Workout Display Flow

```typescript
// 1. Lade Tagesplan
const dayPlan = await fetch(`/api/v2/plan/day/${date}`).then(r => r.json());

// 2. Prüfe Workout Status
if (dayPlan.workout?.status === 'generating') {
  showLoadingState();
} else if (dayPlan.workout?.type === 'rest') {
  showRestDay();
} else if (dayPlan.workout) {
  showWorkoutPreview(dayPlan.workout);
}

// 3. Bei Click auf Workout: Lade Details
const workoutDetails = await fetch(`/api/v2/workouts/${workoutId}`)
  .then(r => r.json());

displayExercises(workoutDetails.exercises);
```

### Exercise Display

Für jede Übung zeige:
- **Name** (auf Deutsch)
- **Sets x Reps** oder **Duration**
- **Rest Time**
- **Weight Recommendation**
- **Form Cues** (Ausführungshinweise)
- **Equipment** benötigt
- **Alternatives** Button für Premium User

### Workout Tracking (Geplant)

```typescript
// Start Workout Session
POST /api/v2/workouts/{id}/start

// Complete Exercise
POST /api/v2/exercises/{id}/complete
{
  "weight_used": 80,
  "reps_completed": [8, 8, 7, 6],
  "notes": "Felt strong today"
}

// Complete Workout
POST /api/v2/workouts/{id}/complete
```

## Kosten-Überlegungen

### Pro User (28 Tage):
- **Workout Days**: ~20 (bei 5-6x/week)
- **Rest Days**: ~8 (keine OpenAI Calls)
- **Cost per Workout**: ~$0.03-0.05
- **Total per User**: ~$0.60-1.00

### Combined (Meals + Workouts):
- **Meals**: ~$0.10-0.20
- **Workouts**: ~$0.60-1.00
- **Total**: ~$0.70-1.20 pro User-Onboarding

### Optimierungen:
- Rest Days ohne AI (✅ implementiert)
- Batch multiple days in einem Request
- Cache ähnliche Workouts
- Progressive Overload ohne AI (nur Gewicht erhöhen)

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
php artisan queue:work
tail -f storage/logs/laravel.log | grep "workout"
```

### API testen:
```bash
# Tagesplan mit Workout
curl http://localhost:8000/api/v2/plan/day/2025-12-20

# Workout Details
curl http://localhost:8000/api/v2/workouts/1
```

## Erweiterungen

### Geplante Features:
- [ ] Workout Progress Tracking
- [ ] Exercise Video Links (YouTube API)
- [ ] Exercise Substitutions (Premium)
- [ ] Workout History & Analytics
- [ ] Progressive Overload Tracking
- [ ] Rest Timer Integration
- [ ] Workout Notes & Photos
- [ ] Social Sharing

### Premium Features:
- [ ] Alternative Exercises
- [ ] Custom Workout Splits
- [ ] Advanced Analytics
- [ ] 1RM Calculator
- [ ] Form Check Videos

## Dateien erstellt/geändert

### Neu erstellt:
- `app/Jobs/GenerateUserWorkoutPlan.php`
- `app/Listeners/GenerateWorkoutPlan.php`
- `app/Models/WorkoutPlan.php`
- `app/Models/Exercise.php`
- `database/migrations/*_create_workout_plans_table.php`
- `database/migrations/*_create_exercises_table.php`
- `docs/WORKOUT_PLAN_GENERATION.md`

### Geändert:
- `app/Models/Plan.php` (workoutPlans Beziehung)
- `app/Providers/AppServiceProvider.php` (Listener Registration)
- `routes/web.php` (Workout API Endpoints)

---

**Status:** ✅ Workout Plan Generation vollständig implementiert!

