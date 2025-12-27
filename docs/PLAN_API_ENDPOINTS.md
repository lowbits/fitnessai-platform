# Plan API Endpoints

## Overview
This document describes the authenticated API endpoints for accessing user plans, meals, and workouts.

## Authentication
All endpoints require Sanctum authentication. Include the bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

---

## Endpoints

### 1. Get Day Plan
Get the meal and workout plan for a specific date.

**Endpoint:** `GET /api/v2/plan/day/{date}`

**Parameters:**
- `date` (string, required): Date in YYYY-MM-DD format

**Response (200 OK):**
```json
{
  "plan_id": 1,
  "plan_day": 5,
  "total_days": 28,
  "date": "2025-01-15",
  "day_name": "Monday",
  "locked": false,
  "status": "generated",
  "meals": [
    {
      "id": 1,
      "name": "Protein Oatmeal with Berries",
      "type": "Breakfast",
      "image": "breakfast_placeholder",
      "calories": 450,
      "protein_g": 25,
      "carbs_g": 60,
      "fat_g": 12
    }
  ],
  "workout": {
    "id": 1,
    "name": "Upper Body Push",
    "type": "strength",
    "description": "Focus on chest, shoulders, and triceps",
    "duration_minutes": 45,
    "exercises_count": 8,
    "difficulty": "Intermediate",
    "muscle_groups": ["Chest", "Shoulders", "Triceps"],
    "status": "generated"
  },
  "daily_totals": {
    "calories": 2200,
    "protein_g": 165,
    "carbs_g": 220,
    "fat_g": 65
  },
  "message": null
}
```

**Response (400 Bad Request):**
```json
{
  "error": "Invalid date format",
  "message": "Please provide a valid date in YYYY-MM-DD format"
}
```

**Response (404 Not Found):**
```json
{
  "error": "No active plan found",
  "message": "You don't have an active plan. Please complete onboarding first."
}
```

**Status Values:**
- `generated`: Plan is fully generated and ready
- `generating`: Plan is currently being generated
- `partial`: Some parts could not be generated
- `failed`: Generation failed
- `not_generated`: Plan has not been generated yet

---

### 2. Get Meal Details
Get detailed information about a specific meal.

**Endpoint:** `GET /api/v2/meals/{mealId}`

**Parameters:**
- `mealId` (integer, required): The meal ID

**Response (200 OK):**
```json
{
  "id": 1,
  "name": "Protein Oatmeal with Berries",
  "type": "Breakfast",
  "image": "breakfast_placeholder",
  "description": "A hearty breakfast packed with protein and fiber to fuel your morning.",
  "nutrition": {
    "calories": 450,
    "protein_g": 25,
    "carbs_g": 60,
    "fat_g": 12,
    "fiber_g": 8,
    "sugar_g": 15
  },
  "ingredients": [
    {
      "name": "Rolled oats",
      "amount": "80",
      "unit": "g"
    },
    {
      "name": "Protein powder (vanilla)",
      "amount": "30",
      "unit": "g"
    },
    {
      "name": "Mixed berries",
      "amount": "150",
      "unit": "g"
    },
    {
      "name": "Almond milk",
      "amount": "250",
      "unit": "ml"
    }
  ],
  "instructions": [
    "Combine oats and almond milk in a bowl",
    "Microwave for 2-3 minutes or cook on stovetop",
    "Stir in protein powder",
    "Top with mixed berries",
    "Optional: Add honey or cinnamon to taste"
  ],
  "prep_time_minutes": 5,
  "cook_time_minutes": 5,
  "total_time_minutes": 10,
  "difficulty": "Easy",
  "servings": 1,
  "tags": ["High Protein", "Breakfast", "Quick"],
  "allergens": ["Dairy", "Tree Nuts"]
}
```

**Response (403 Forbidden):**
```json
{
  "error": "Unauthorized",
  "message": "You do not have access to this meal"
}
```

**Response (404 Not Found):**
```json
{
  "error": "Meal not found",
  "message": "The requested meal does not exist"
}
```

---

### 3. Get Workout Details
Get detailed information about a specific workout, including all exercises.

**Endpoint:** `GET /api/v2/workouts/{workoutId}`

**Parameters:**
- `workoutId` (integer, required): The workout ID

**Response (200 OK):**
```json
{
  "id": 1,
  "name": "Upper Body Push",
  "type": "strength",
  "description": "Focus on chest, shoulders, and triceps with compound and isolation movements.",
  "estimated_duration_minutes": 45,
  "estimated_calories_burned": 280,
  "difficulty": "Intermediate",
  "muscle_groups": ["Chest", "Shoulders", "Triceps"],
  "exercises": [
    {
      "id": 1,
      "order": 1,
      "name": "Barbell Bench Press",
      "type": "strength",
      "description": "Compound movement targeting chest, shoulders, and triceps",
      "sets": 4,
      "reps": 8,
      "duration_seconds": null,
      "rest_seconds": "90-120",
      "tempo": "3-0-1-0",
      "weight_recommendation": "70-80% of 1RM",
      "muscle_groups": ["Chest", "Shoulders", "Triceps"],
      "equipment": ["Barbell", "Bench"],
      "form_cues": "Keep feet flat, retract shoulder blades, lower bar to mid-chest, press explosively",
      "alternatives": ["Dumbbell Bench Press", "Push-ups"],
      "difficulty": "Intermediate",
      "video_url": null,
      "image": null
    }
  ],
  "exercises_count": 8
}
```

**Response (403 Forbidden):**
```json
{
  "error": "Unauthorized",
  "message": "You do not have access to this workout"
}
```

**Response (404 Not Found):**
```json
{
  "error": "Workout not found",
  "message": "The requested workout does not exist"
}
```

---

## Error Handling

All endpoints follow consistent error response format:

```json
{
  "error": "Error type",
  "message": "Human-readable error message"
}
```

### Common HTTP Status Codes:
- `200 OK`: Successful request
- `400 Bad Request`: Invalid request parameters
- `401 Unauthorized`: Missing or invalid authentication token
- `403 Forbidden`: Authenticated but not authorized to access resource
- `404 Not Found`: Resource not found
- `500 Internal Server Error`: Server error

---

## Rate Limiting

API endpoints are protected by rate limiting:
- **Authentication endpoints**: 5 requests per minute
- **Plan/Meal/Workout endpoints**: Standard rate limit (60 requests per minute)

---

## Testing

### Using cURL:

```bash
# Login first to get token
curl -X POST https://fitness-ai.me/api/v2/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}'

# Get day plan
curl https://fitness-ai.me/api/v2/plan/day/2025-01-15 \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get meal details
curl https://fitness-ai.me/api/v2/meals/1 \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get workout details
curl https://fitness-ai.me/api/v2/workouts/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Using React Native:

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'https://fitness-ai.me/api/v2',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  },
});

// Get day plan
const dayPlan = await api.get('/plan/day/2025-01-15');

// Get meal details
const meal = await api.get('/meals/1');

// Get workout details
const workout = await api.get('/workouts/1');
```

---

## Future Enhancements

Planned features:
- ✅ Subscription-based locking (free vs. premium)
- 🔄 Meal regeneration
- 🔄 Exercise alternatives
- 🔄 Progress tracking (workout completion, meal logging)
- 🔄 Streak tracking
- 🔄 Calendar view of all plan days

