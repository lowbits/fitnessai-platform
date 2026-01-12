# Test Fix Guide - Adding target_date

## Problem
All tests are failing because `target_date` is now **required**, but most tests don't provide it.

## Solution Pattern

### Before:
```php
$response = $this->actingAs($this->user, 'sanctum')
    ->postJson("/api/v2/workouts/{$workout->id}/reschedule");
```

### After:
```php
$tomorrowDate = now()->addDays(6); // or appropriate date
$response = $this->actingAs($this->user, 'sanctum')
    ->postJson("/api/v2/workouts/{$workout->id}/reschedule", [
        'target_date' => $tomorrowDate->format('Y-m-d'),
    ]);
```

## Tests to Fix

All tests that call `/reschedule` without `target_date` parameter need to be updated.

## Quick Fix Command

For manual bulk fixing, search for:
```
->postJson("/api/v2/workouts/{$
```

And ensure each has a `target_date` in the request body.


