# Meal Replacement Workflow - Quick Reference

## Complete User Flow

### Step 1: Get Alternative Meal Titles
```http
POST /api/v2/meals/{meal}/alternatives
Authorization: Bearer {token}
Content-Type: application/json

{
  "hint": "I want something with salmon" // optional
}
```

**Response (200 OK):**
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
    "name": "Chicken Salad",
    "type": "lunch",
    "calories": 500,
    "protein_g": 40,
    "carbs_g": 35,
    "fat_g": 20
  }
}
```

### Step 2: Replace Meal with Selected Title
```http
POST /api/v2/meals/{meal}/replace
Authorization: Bearer {token}
Content-Type: application/json

{
  "instruction": "Grilled Salmon with Lemon Herb Quinoa"
}
```

**Response (202 Accepted):**
```json
{
  "message": "Meal replacement is being generated",
  "meal_id": 123,
  "instruction": "Grilled Salmon with Lemon Herb Quinoa"
}
```

### Step 3: Job Processes in Background
The `ReplaceMealJob` generates a complete recipe including:
- Full ingredient list with amounts
- Step-by-step cooking instructions
- Accurate nutritional information
- Prep and cook times
- Difficulty level
- Tags and allergens

### Step 4: User Sees Updated Meal
The original meal (ID 123) is updated with the new recipe data. The user can refresh their meal plan to see the changes.

## Key Differences from Original Implementation

### Before (Old)
```http
POST /api/v2/meals/{mealId}/replace
{
  "hint": "something with salmon" // optional, vague
}
```
- Used integer `mealId` parameter
- Manual authorization checks
- Vague hint could mean anything
- No way to preview what would be generated

### After (New)
```http
# Step 1: Get previews
POST /api/v2/meals/{meal}/alternatives
{
  "hint": "I want something with salmon" // optional
}

# Step 2: Select specific recipe title
POST /api/v2/meals/{meal}/replace
{
  "instruction": "Grilled Salmon with Lemon Herb Quinoa" // required, specific
}
```
- Uses route model binding
- Policy-based authorization (cleaner)
- Specific recipe title ensures predictable results
- Two-step process allows user to preview options

## Frontend Integration Example

```javascript
// Component: MealCard.jsx

const [alternatives, setAlternatives] = useState(null);
const [isReplacing, setIsReplacing] = useState(false);

// Step 1: Get alternatives
const handleGetAlternatives = async (meal, hint = null) => {
  try {
    const response = await fetch(`/api/v2/meals/${meal.id}/alternatives`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ hint })
    });
    
    const data = await response.json();
    setAlternatives(data);
  } catch (error) {
    console.error('Failed to get alternatives:', error);
  }
};

// Step 2: Replace with selected title
const handleReplaceMeal = async (meal, recipeTitle) => {
  try {
    setIsReplacing(true);
    
    const response = await fetch(`/api/v2/meals/${meal.id}/replace`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ instruction: recipeTitle })
    });
    
    const data = await response.json();
    
    // Show success message
    toast.success('Meal replacement is being generated!');
    
    // Poll for updates or wait for websocket notification
    // ... implementation depends on your notification system
    
  } catch (error) {
    console.error('Failed to replace meal:', error);
  } finally {
    setIsReplacing(false);
  }
};

// UI
return (
  <div>
    <button onClick={() => handleGetAlternatives(meal)}>
      Find Alternatives
    </button>
    
    {alternatives && (
      <div className="alternatives-list">
        <h3>Select a recipe:</h3>
        {alternatives.titles.map(title => (
          <button 
            key={title}
            onClick={() => handleReplaceMeal(meal, title)}
            disabled={isReplacing}
          >
            {title}
          </button>
        ))}
      </div>
    )}
  </div>
);
```

## Testing Checklist

- ✅ Get alternatives without hint
- ✅ Get alternatives with hint
- ✅ Replace meal with recipe title
- ✅ Authorization (user must own meal)
- ✅ Validation (required instruction, max length)
- ✅ All meal types (breakfast, lunch, dinner, snack)
- ✅ Special characters in titles
- ✅ Authentication required
- ✅ Job is dispatched correctly

## Error Scenarios

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```
**Solution**: Include valid Bearer token in Authorization header

### 403 Forbidden
```json
{
  "message": "This action is unauthorized."
}
```
**Solution**: Meal doesn't belong to the authenticated user

### 422 Validation Error
```json
{
  "message": "The instruction field is required.",
  "errors": {
    "instruction": ["The instruction field is required."]
  }
}
```
**Solution**: Include `instruction` in request body

### 500 Internal Server Error (Alternatives)
```json
{
  "error": "Failed to generate alternatives",
  "message": "An error occurred while generating meal alternatives. Please try again."
}
```
**Solution**: OpenAI API issue, try again or check logs

## Performance Notes

- **Get Alternatives**: ~2-3 seconds (synchronous, waits for OpenAI)
- **Replace Meal**: ~100ms (async, returns immediately with 202)
- **Job Processing**: ~5-10 seconds (background, generates full recipe)

## Best Practices

1. **Always show alternatives first** - Don't let users blindly replace meals
2. **Show loading states** - Both operations take time
3. **Handle errors gracefully** - Network issues, API limits, etc.
4. **Poll or use websockets** - To notify when replacement is complete
5. **Cache alternatives** - If user dismisses modal and reopens, show same results
6. **Add confirmation** - Before replacing, confirm with user
7. **Allow cancellation** - User should be able to cancel if they change their mind
8. **Show nutritional comparison** - Help users make informed decisions

## API Design Principles

This implementation follows these principles:

1. **Resource-based URLs**: `/meals/{meal}/alternatives`, `/meals/{meal}/replace`
2. **HTTP verbs correctly**: POST for both (creating alternatives, creating replacement)
3. **Consistent responses**: JSON with clear structure
4. **Proper status codes**: 200 OK, 202 Accepted, 403 Forbidden, 422 Validation Error
5. **Policy-based auth**: Centralized authorization logic
6. **Validation**: Clear error messages for invalid input
7. **Async when appropriate**: Long-running tasks use job queues
8. **Idempotency**: Replacing same meal multiple times is safe

