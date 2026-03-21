# Plan: Calendar Entries API Endpoint

## Context
The mobile app needs a way to show calendar pills/badges indicating what's planned for each day (workouts and meals). Currently only a single-day endpoint exists (`/api/v2/plan/day/{date}`), requiring one call per day. A bulk calendar endpoint will let the app fetch a date range efficiently.

## Endpoint

`GET /api/v2/plan/calendar?start_date=2026-03-01&end_date=2026-03-31`

Sanctum auth, max 31-day range.

## Response

```json
{
  "plan_id": 1,
  "start_date": "2026-03-01",
  "end_date": "2026-03-31",
  "days": [
    {
      "date": "2026-03-01",
      "has_workout": true,
      "has_meals": true,
      "workout_completed": true,
      "meals_completed": false,
      "workout_type": "strength",
      "workout_status": "generated",
      "meal_status": "generated"
    }
  ]
}
```

Days outside plan range are included with all booleans `false` and statuses `null`.

## Implementation

### 1. Form Request: `app/Http/Requests/CalendarEntriesRequest.php`
- `start_date`: required, date_format:Y-m-d
- `end_date`: required, date_format:Y-m-d, after_or_equal:start_date
- Custom rule: max 31 days between dates
- Helper methods: `getStartDate()`, `getEndDate()` returning Carbon

### 2. Controller: add `getCalendarEntries()` to `PlanController`
- 4 queries total (no N+1):
  1. Active plan lookup
  2. `WorkoutPlan::whereBetween('date', ...)` keyed by date string
  3. `MealPlan::whereBetween('date', ...)->with('meals:id,meal_plan_id,completed_at')` keyed by date string
  4. `WorkoutTracking::whereIn('workout_plan_id', ...)->whereNotNull('completed_at')` for completion
- Loop through each day in range, build response array

### 3. Route: `routes/api.php`
```php
Route::get('/plan/calendar', [PlanController::class, 'getCalendarEntries']);
```

### 4. Test: `tests/Feature/Api/V2/PlanCalendarTest.php`
- Returns calendar entries for date range
- Returns 404 when no active plan
- Validates required dates, format, max range, end >= start
- workout_completed reflects tracking
- meals_completed only true when ALL meals completed
- Requires authentication

## Files to modify
- `app/Http/Controllers/Api/V2/PlanController.php` — add method
- `routes/api.php` — add route
- `app/Http/Requests/CalendarEntriesRequest.php` — create (artisan)
- `tests/Feature/Api/V2/PlanCalendarTest.php` — create (artisan)

## Verification
- Run `php artisan test --compact --filter=PlanCalendar`
- Run `vendor/bin/pint --dirty`
