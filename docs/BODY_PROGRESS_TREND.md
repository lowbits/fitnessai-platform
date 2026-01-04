# Body Progress Trend Feature

## Overview
The body progress tracking API automatically calculates and returns a weight trend indicator that shows whether the user's weight is going up, down, or staying stable compared to their previous entry.

## Implementation Details

### Backend Calculation
The trend is calculated on the **backend** (not frontend) for the following reasons:
- **Single Source of Truth**: Logic is centralized and consistent across all clients
- **Less Duplication**: Frontend doesn't need to implement the same logic
- **Easier Maintenance**: Changes only need to be made in one place
- **Better Performance**: Backend can efficiently query previous entries
- **Consistency**: All clients see the same trends

### Trend Values
The `trend` field in the API response can have the following values:

- `"up"`: Weight has increased by more than 0.1kg compared to the previous entry
- `"down"`: Weight has decreased by more than 0.1kg compared to the previous entry
- `"stable"`: Weight change is less than 0.1kg (considered negligible)
- `null`: No previous entry exists (first entry for the user)

### API Response Example

```json
{
  "message": "Body progress tracked successfully",
  "data": {
    "id": 1,
    "user_id": 123,
    "weight": "75.50",
    "body_fat_percentage": "18.50",
    "muscle_mass": "65.00",
    "trend": "down",
    "recorded_at": "2026-01-04T10:30:00.000000Z",
    "created_at": "2026-01-04T10:30:00.000000Z",
    "updated_at": "2026-01-04T10:30:00.000000Z"
  }
}
```

## Usage

### Mobile App Example
```javascript
const bodyProgress = response.data.data;

if (bodyProgress.trend === 'down') {
  // Show green indicator / success message
  showMessage('Great progress! Your weight is decreasing.');
} else if (bodyProgress.trend === 'up') {
  // Show yellow/orange indicator
  showMessage('Your weight has increased.');
} else if (bodyProgress.trend === 'stable') {
  // Show blue indicator
  showMessage('Your weight is stable.');
} else {
  // First entry, no previous data to compare
  showMessage('First weight entry recorded!');
}
```

### Web App Example
```javascript
const getTrendIcon = (trend) => {
  switch (trend) {
    case 'down': return '↓'; // Green
    case 'up': return '↑';   // Red/Orange
    case 'stable': return '→'; // Blue
    default: return '';
  }
};
```

## Technical Implementation

### Model Method
Located in `app/Models/BodyProgress.php`:

```php
public function getTrendAttribute(): ?string
{
    if (!$this->weight_kg) {
        return null;
    }

    $previousEntry = static::where('user_id', $this->user_id)
        ->where('recorded_at', '<', $this->recorded_at)
        ->whereNotNull('weight_kg')
        ->orderBy('recorded_at', 'desc')
        ->first();

    if (!$previousEntry || !$previousEntry->weight_kg) {
        return null;
    }

    $difference = $this->weight_kg - $previousEntry->weight_kg;
    
    // Consider differences less than 0.1kg as stable
    if (abs($difference) < 0.1) {
        return 'stable';
    }

    return $difference > 0 ? 'up' : 'down';
}
```

## Testing

All trend scenarios are covered by tests in `tests/Feature/BodyProgressTrackingTest.php`:

- ✅ First entry returns `null` trend
- ✅ Weight increase returns `"up"`
- ✅ Weight decrease returns `"down"`
- ✅ Minimal weight change returns `"stable"`
- ✅ Trend only compares within same user (not across users)

Run tests:
```bash
php artisan test --filter BodyProgressTrackingTest
```

## Future Enhancements

Potential future improvements:
- Add trend for other metrics (body fat percentage, muscle mass, etc.)
- Calculate longer-term trends (weekly/monthly averages)
- Add trend percentage/amount in addition to direction
- Support different measurement systems (kg vs lbs) with appropriate thresholds

