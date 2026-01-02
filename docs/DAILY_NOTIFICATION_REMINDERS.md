# Daily Notification Reminders - Implementation

## Overview
Automatic daily reminders for workouts and meals sent via Expo Push Notifications.

## Features

### ✅ Workout Reminders (8:00 AM daily)
- Sends personalized workout reminder for today's workout
- Special rest day message for rest days
- Bilingual support (English & German)
- Only for users with:
  - Active devices registered
  - Active plan
  - Generated workout plan for today

### ✅ Meal Reminders (multiple times daily)
- **Breakfast**: 7:30 AM
- **Lunch**: 12:00 PM
- **Snack**: 3:30 PM
- **Dinner**: 6:30 PM
- Bilingual support (English & German)
- Only for users with:
  - Active devices registered
  - Active plan
  - Generated meal plan for today

## Notifications

### Workout Reminder (Regular Day)
**English:**
- Title: 💪 Time for Your Workout!
- Body: Ready to crush {Workout Name}? Let's get moving!

**German:**
- Title: 💪 Zeit für dein Training!
- Body: Bereit für {Workout Name}? Lass uns loslegen!

### Rest Day Reminder
**English:**
- Title: 🌟 Rest Day - Enjoy!
- Body: Your body needs rest to grow stronger. Take it easy today and recharge!

**German:**
- Title: 🌟 Ruhetag - Genieße ihn!
- Body: Dein Körper braucht Ruhe um stärker zu werden. Entspann dich heute und tanke Energie!

### Meal Reminders
**Breakfast:**
- 🌅 EN: Time for Breakfast! / DE: Zeit fürs Frühstück!

**Lunch:**
- 🍽️ EN: Lunch Time! / DE: Zeit fürs Mittagessen!

**Snack:**
- 🥗 EN: Snack Time! / DE: Zeit für einen Snack!

**Dinner:**
- 🌙 EN: Dinner Time! / DE: Zeit fürs Abendessen!

## Schedule

```php
// Workout reminders - 8:00 AM
Schedule::command('notifications:workout-reminders')
    ->dailyAt('08:00');

// Breakfast - 7:30 AM
Schedule::command('notifications:meal-reminders breakfast')
    ->dailyAt('07:30');

// Lunch - 12:00 PM
Schedule::command('notifications:meal-reminders lunch')
    ->dailyAt('12:00');

// Snack - 3:30 PM
Schedule::command('notifications:meal-reminders snack')
    ->dailyAt('15:30');

// Dinner - 6:30 PM
Schedule::command('notifications:meal-reminders dinner')
    ->dailyAt('18:30');
```

## Commands

### Test Workout Reminders
```bash
php artisan notifications:workout-reminders
```

### Test Meal Reminders
```bash
# Test breakfast
php artisan notifications:meal-reminders breakfast

# Test lunch
php artisan notifications:meal-reminders lunch

# Test snack
php artisan notifications:meal-reminders snack

# Test dinner
php artisan notifications:meal-reminders dinner
```

## How It Works

### Workout Reminders
1. Finds all users with:
   - At least one registered device
   - Active plan (status = 'active')
   - Plan is fully generated (generation_completed_at is set)
2. For each user:
   - Gets today's workout plan
   - Checks workout_type
   - If 'rest': Sends rest day message
   - Otherwise: Sends workout reminder with workout name
3. Uses user's locale (en/de) for message

### Meal Reminders
1. Finds all users with:
   - At least one registered device
   - Active plan (status = 'active')
   - Plan is fully generated
2. For each user:
   - Gets today's meal plan
   - Finds specific meal by meal_type (breakfast/lunch/snack/dinner)
   - Sends reminder with meal name
3. Uses user's locale (en/de) for message

## Filtering Logic

### Only Generated Plans
```php
->whereHas('plans', function ($query) {
    $query->where('status', 'active')
        ->where('generation_completed_at', '!=', null);
})
```

### Only Today's Workout/Meals
```php
->whereDate('date', today())
->where('status', 'generated')
```

## Notification Data

### Workout Reminder
```json
{
  "type": "workout_reminder",
  "workout_id": 123,
  "screen": "WorkoutDetail"
}
```

### Rest Day Reminder
```json
{
  "type": "rest_day_reminder",
  "screen": "Home"
}
```

### Meal Reminder
```json
{
  "type": "meal_reminder",
  "meal_type": "lunch",
  "meal_id": 456,
  "screen": "MealDetail"
}
```

## React Native Handling

### Handle Notification Tap
```typescript
responseListener.current = Notifications.addNotificationResponseReceivedListener(
  response => {
    const data = response.notification.request.content.data;
    
    switch (data.type) {
      case 'workout_reminder':
        navigation.navigate('WorkoutDetail', { workoutId: data.workout_id });
        break;
        
      case 'rest_day_reminder':
        navigation.navigate('Home');
        break;
        
      case 'meal_reminder':
        navigation.navigate('MealDetail', { mealId: data.meal_id });
        break;
    }
  }
);
```

## Cron Setup (Server)

Make sure the Laravel scheduler is running:

```bash
# Add to crontab (crontab -e)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Or use Supervisor for Laravel Queue Worker:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path-to-your-project/storage/logs/worker.log
```

## Monitoring

Check logs for notification sending:
```bash
# View command output
tail -f storage/logs/laravel.log

# Test commands manually
php artisan notifications:workout-reminders
php artisan notifications:meal-reminders breakfast
```

## Customization

### Change Times
Edit `routes/console.php` and adjust `dailyAt()` times:
```php
Schedule::command('notifications:meal-reminders breakfast')
    ->dailyAt('07:00'); // Change from 07:30 to 07:00
```

### Add New Meal Types
1. Add to `SendMealReminders::MEAL_TYPES`
2. Update `MealReminderNotification` with new emoji and messages
3. Add schedule in `routes/console.php`

### Disable Specific Reminders
Comment out unwanted schedules in `routes/console.php`:
```php
// Disable snack reminders
// Schedule::command('notifications:meal-reminders snack')
//     ->dailyAt('15:30');
```

## User Preferences (Future Enhancement)

Consider adding user preferences:
- Enable/disable workout reminders
- Enable/disable meal reminders per meal type
- Custom reminder times
- Quiet hours

Add columns to users table:
```php
$table->json('notification_preferences')->nullable();
```

Example structure:
```json
{
  "workout_reminders": true,
  "meal_reminders": {
    "breakfast": true,
    "lunch": true,
    "snack": false,
    "dinner": true
  },
  "custom_times": {
    "workout": "08:00",
    "breakfast": "07:30"
  }
}
```

## Troubleshooting

### Notifications Not Sending
1. Check cron is running: `crontab -l`
2. Check queue workers are running
3. Verify users have devices: `php artisan tinker` → `User::whereHas('devices')->count()`
4. Check logs: `tail -f storage/logs/laravel.log`

### Wrong Language
- Verify user's `locale` column is set correctly (en/de)
- Default is 'en' if locale is null

### Notifications Sent Multiple Times
- Commands use `withoutOverlapping()` to prevent this
- Check if multiple queue workers are processing the same job

## Performance

- Commands run in background with `runInBackground()`
- Notifications are queued with `ShouldQueue`
- Use `withoutOverlapping()` to prevent concurrent runs
- Efficient queries with proper eager loading

## Testing in Development

Test immediately without waiting for schedule:
```bash
php artisan notifications:workout-reminders
php artisan notifications:meal-reminders breakfast
```

## Production Deployment

1. Ensure scheduler is in crontab
2. Start queue workers
3. Monitor logs for first few days
4. Adjust times based on user feedback
5. Consider timezone support for international users

---

**All reminders are professional, motivational, and respect user's language preference!** 🎯

