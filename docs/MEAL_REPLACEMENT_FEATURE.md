# Meal Replacement Feature

## Overview

The meal replacement feature allows users to replace meals in their meal plans with alternative recipes. This is a two-step process:

1. **Get Alternatives** (`GetMealAlternativesController`) - User requests 5 alternative meal title suggestions
2. **Replace Meal** (`ReplaceMealController`) - User selects one of the titles and the full recipe is generated

## Architecture

### Controllers

#### GetMealAlternativesController
- **Route**: `POST /api/v2/meals/{meal}/alternatives`
- **Purpose**: Generate 5 alternative meal titles based on the original meal's nutritional profile
- **Authorization**: User must own the meal (via `MealPolicy::update`)
- **Input**:
  - `hint` (optional, string, max 500): User preference for alternatives (e.g., "I want something with salmon")
- **Output**:
  ```json
  {
    "titles": [
      "Grilled Salmon with Lemon Herb Quinoa",
      "Pan-Seared Salmon with Garlic Asparagus",
      "Salmon Teriyaki Bowl with Edamame",
      "Grilled Salmon Caesar Salad",
      "Honey Mustard Glazed Salmon with Quinoa"
    ],
    "original_meal": {
      "id": 123,
      "name": "Old Lunch",
      "type": "lunch",
      "calories": 500,
      "protein_g": 40,
      "carbs_g": 35,
      "fat_g": 20
    }
  }
  ```

#### ReplaceMealController
- **Route**: `POST /api/v2/meals/{meal}/replace`
- **Purpose**: Replace a meal with a specific recipe based on a recipe title instruction
- **Authorization**: User must own the meal (via `MealPolicy::update`)
- **Input**:
  - `instruction` (required, string, max 500): The recipe title to create (typically from the alternatives list)
- **Output**:
  ```json
  {
    "message": "Meal replacement is being generated",
    "meal_id": 123,
    "instruction": "Grilled Salmon with Lemon Herb Quinoa"
  }
  ```
- **Status**: 202 Accepted (async processing via job queue)

### Jobs

#### ReplaceMealJob
- **Queue**: `nutrition`
- **Purpose**: Generate a complete recipe based on the instruction (recipe title)
- **Process**:
  1. Loads user profile and nutritional requirements
  2. Calls OpenAI Responses API with the recipe title instruction
  3. Uses `replace_meal` function tool to generate complete recipe
  4. Updates the existing meal record with new data
  5. Recalculates meal plan totals

### Models

#### Meal
- **Policy**: `MealPolicy`
- **Authorization**: `update` method checks if user owns the meal via `meal.mealPlan.plan.user_id`

## User Flow

### Typical Workflow

1. **User views their meal plan** and decides they want to change lunch
2. **User requests alternatives**:
   ```javascript
   POST /api/v2/meals/456/alternatives
   {
     "hint": "I want something with salmon"
   }
   ```
3. **System returns 5 recipe titles** that match the meal's nutritional profile
4. **User selects a title** (e.g., "Grilled Salmon with Lemon Herb Quinoa")
5. **User requests replacement**:
   ```javascript
   POST /api/v2/meals/456/replace
   {
     "instruction": "Grilled Salmon with Lemon Herb Quinoa"
   }
   ```
6. **System queues the job** and returns 202 Accepted
7. **Job generates the full recipe** in the background
8. **User can refresh** to see the updated meal

## Technical Details

### OpenAI Integration

#### GetMealAlternativesController
- **Model**: `gpt-4o-mini`
- **API**: Responses API
- **Tool**: `provide_meal_titles` function
- **Output**: 5 meal titles only (no full recipes)

#### ReplaceMealJob
- **Model**: `gpt-5-mini`
- **API**: Responses API
- **Tool**: `replace_meal` function
- **Output**: Complete recipe with:
  - Name, description
  - Full nutritional breakdown
  - Ingredients with amounts and units
  - Step-by-step instructions
  - Prep/cook times, difficulty
  - Tags and allergens

### Nutritional Matching

Both the alternatives and replacement maintain similar macros (±10-15%):
- Calories
- Protein
- Carbohydrates
- Fat

### Diet Compliance

The system respects the user's diet type from their profile:
- Vegan
- Vegetarian
- Pescatarian
- Keto
- Paleo
- Mediterranean
- etc.

### Localization

Both endpoints respect the user's locale:
- `de` (German) - All text in German
- `en` (English) - All text in English

## Testing

### MealAlternativesTest.php
Tests for getting alternative meal titles:
- ✅ User can get 5 meal title alternatives without hint
- ✅ User can get title alternatives with hint
- ✅ User cannot get alternatives for meal they do not own
- ✅ Alternatives validation limits hint length (max 500)

### MealReplacementTest.php
Tests for replacing meals with recipe titles:
- ✅ User can replace a meal with a recipe title instruction
- ✅ User cannot replace a meal without instruction
- ✅ User cannot replace a meal they do not own
- ✅ Replacement validation limits instruction length (max 500)
- ✅ User can replace breakfast/lunch/dinner/snack meals
- ✅ Replacement works for meals with special characters
- ✅ Unauthenticated user cannot replace meal
- ✅ Instruction must be a string
- ✅ Empty string instruction is rejected

## Error Handling

### GetMealAlternativesController
- **403 Forbidden**: User doesn't own the meal
- **422 Validation Error**: Invalid hint (too long)
- **500 Internal Error**: OpenAI API failure

### ReplaceMealController
- **401 Unauthorized**: User not authenticated
- **403 Forbidden**: User doesn't own the meal
- **422 Validation Error**: Missing or invalid instruction

### ReplaceMealJob
- Logs all errors
- Throws exception on failure (will retry based on queue config)
- Updates meal plan totals after successful replacement

## Database Changes

The meal replacement updates the following fields in the `meals` table:
- `name`
- `description`
- `calories`
- `protein_g`
- `carbs_g`
- `fat_g`
- `fiber_g`
- `sugar_g`
- `ingredients` (JSON)
- `instructions` (JSON)
- `prep_time_minutes`
- `cook_time_minutes`
- `difficulty`
- `tags` (JSON)
- `allergens` (JSON)

The `meal_plan` totals are also updated:
- `total_calories`
- `total_protein_g`
- `total_carbs_g`
- `total_fat_g`

## API Routes

```php
// Get alternative meal titles
Route::post('/meals/{meal}/alternatives', GetMealAlternativesController::class)
    ->name('meals.alternatives');

// Replace meal with specific recipe title
Route::post('/meals/{meal}/replace', ReplaceMealController::class)
    ->name('meals.replace');
```

Both routes require authentication via `auth:sanctum` middleware.

## Example Usage

### Get Alternatives (No Hint)

**Request:**
```http
POST /api/v2/meals/123/alternatives
Authorization: Bearer {token}
Content-Type: application/json
```

**Response:**
```json
{
  "titles": [
    "Mediterranean Quinoa Bowl with Grilled Chicken",
    "Thai Peanut Chicken Stir-Fry with Vegetables",
    "Lemon Herb Baked Chicken with Sweet Potato",
    "Mexican Chicken Burrito Bowl with Black Beans",
    "Asian Glazed Chicken with Broccoli and Brown Rice"
  ],
  "original_meal": {
    "id": 123,
    "name": "Chicken and Rice",
    "type": "lunch",
    "calories": 550,
    "protein_g": 45,
    "carbs_g": 50,
    "fat_g": 15
  }
}
```

### Get Alternatives (With Hint)

**Request:**
```http
POST /api/v2/meals/123/alternatives
Authorization: Bearer {token}
Content-Type: application/json

{
  "hint": "I want something vegetarian"
}
```

**Response:**
```json
{
  "titles": [
    "Chickpea and Spinach Curry with Quinoa",
    "Mediterranean Stuffed Bell Peppers",
    "Lentil and Vegetable Stir-Fry",
    "Black Bean and Sweet Potato Tacos",
    "Grilled Portobello Mushroom Bowl with Tahini"
  ],
  "original_meal": { ... }
}
```

### Replace Meal

**Request:**
```http
POST /api/v2/meals/123/replace
Authorization: Bearer {token}
Content-Type: application/json

{
  "instruction": "Chickpea and Spinach Curry with Quinoa"
}
```

**Response:**
```json
{
  "message": "Meal replacement is being generated",
  "meal_id": 123,
  "instruction": "Chickpea and Spinach Curry with Quinoa"
}
```

## Implementation Notes

1. **Two-Step Process**: Separating alternatives from replacement allows users to preview options before committing to a change
2. **Async Processing**: Meal replacement uses job queues because generating a full recipe takes 5-10 seconds
3. **Instruction as Title**: The instruction parameter is specifically the recipe title, making it clear what will be generated
4. **Policy-Based Authorization**: Uses Laravel's Gate and Policy system for consistent authorization
5. **Route Model Binding**: Controllers use implicit route model binding for cleaner code
6. **Validation**: Uses Laravel's Validator for consistent validation across both endpoints

## Future Enhancements

Potential improvements:
- [ ] Add websocket/pusher notification when replacement is complete
- [ ] Cache alternative titles for recently replaced meals
- [ ] Add ability to regenerate if user doesn't like the result
- [ ] Track which alternatives are most popular
- [ ] Allow custom instructions beyond the suggested titles
- [ ] Add images for generated meals (via DALL-E)

