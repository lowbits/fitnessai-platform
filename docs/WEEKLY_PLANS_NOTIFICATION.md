# Weekly Plans Notification - Quick Reference

## ✅ Was wurde implementiert

### 1. Notification beim Plan-Generate
Wenn neue Pläne generiert werden, bekommt der User eine motivierende Push Notification.

**WICHTIG:** Notification wird **nicht um 00:00** gesendet, sondern um **08:00 morgens**!

### 2. Timeline

```
00:00 Uhr - Command läuft
   ├─ Prüft welche User heute ihren Generation Day haben
   ├─ Generiert nächste 7 Tage (Workouts + Meals)
   ├─ Queued Notification mit 8-Stunden Delay
   └─ Command beendet

08:00 Uhr - User bekommt Notification
   "🎯 Your Week is Ready!"
```

### 3. Notification Messages

#### Englisch:
```
🎯 Your Week is Ready!
We've prepared your next 7 days of workouts and meals. Let's crush it! 💪
```

#### Deutsch:
```
🎯 Deine Woche ist bereit!
Wir haben die nächsten 7 Tage für dich geplant. Lass uns loslegen! 💪
```

### 4. Notification Data

```json
{
  "type": "weekly_plans_generated",
  "days_generated": 7,
  "start_date": "2026-01-10",
  "end_date": "2026-01-16",
  "screen": "Plans"
}
```

## Implementation Details

### Delayed Notification Logic

```php
// Calculate notification time (08:00 AM)
$notificationTime = now()->setHour(8)->setMinute(0)->setSecond(0);

// If it's already past 08:00, send tomorrow at 08:00
if (now()->hour >= 8) {
    $notificationTime = $notificationTime->addDay();
}

// Calculate delay in seconds
$delay = now()->diffInSeconds($notificationTime);

// Queue notification with delay
$user->notify(
    (new WeeklyPlansGeneratedNotification(
        $daysToGenerate,
        $startDate->format('Y-m-d'),
        $endDate->format('Y-m-d')
    ))->delay($delay)
);
```

### Why 08:00 AM?

✅ **User-Friendly**: People are awake and checking their phones
✅ **Motivation**: Perfect time to plan the day/week ahead
✅ **No Disturbance**: Not during sleep hours
✅ **Engagement**: High chance of opening the app

## React Native Handling

### Handle Notification Tap

```typescript
responseListener.current = Notifications.addNotificationResponseReceivedListener(
  response => {
    const data = response.notification.request.content.data;
    
    if (data.type === 'weekly_plans_generated') {
      // Navigate to Plans screen
      navigation.navigate('Plans', {
        startDate: data.start_date,
        endDate: data.end_date,
      });
    }
  }
);
```

### Foreground Handling

```typescript
notificationListener.current = Notifications.addNotificationReceivedListener(
  notification => {
    const data = notification.request.content.data;
    
    if (data.type === 'weekly_plans_generated') {
      // Show in-app message or banner
      showToast('🎯 Your new week is ready!');
      
      // Refresh plans data
      refetchPlans();
    }
  }
);
```

## Command Output

```
✅ Queued generation for user 1 (john@example.com)
   Generation Day: Thursday
   Start: 2026-01-10 | End: 2026-01-16 | Days: 7
   📱 Notification scheduled for: 2026-01-09 08:00
```

## Example Scenarios

### Scenario 1: Command runs at 00:00
```
00:00 - Generation starts
00:00 - Notification queued with 8h delay
08:00 - User receives notification ✅
```

### Scenario 2: Manual command at 10:00
```
10:00 - Generation starts (manual)
10:00 - Already past 08:00 → schedule for tomorrow
Next day 08:00 - User receives notification ✅
```

### Scenario 3: Command with --force at 06:00
```
06:00 - Generation starts
06:00 - Notification queued with 2h delay
08:00 - User receives notification ✅
```

## Queue Requirements

**Important:** Make sure queue workers are running!

```bash
# Start queue worker
php artisan queue:work

# Or use Supervisor in production
```

### Supervisor Config

```ini
[program:laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/project/storage/logs/queue-worker.log
```

## Testing

### Test Notification Sending

```php
use App\Notifications\WeeklyPlansGeneratedNotification;
use App\Models\User;

$user = User::find(1);

// Send immediately (for testing)
$user->notify(new WeeklyPlansGeneratedNotification(
    7, // days
    '2026-01-10', // start
    '2026-01-16'  // end
));
```

### Test with Delay

```php
// Send in 10 seconds (for testing)
$user->notify(
    (new WeeklyPlansGeneratedNotification(7, '2026-01-10', '2026-01-16'))
        ->delay(10)
);
```

### Check Queue

```bash
# See queued jobs
php artisan queue:monitor

# Process queue manually
php artisan queue:work --once
```

## Monitoring

### Check Logs

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Queue worker logs
tail -f storage/logs/queue-worker.log
```

### Failed Jobs

```bash
# List failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {job-id}

# Retry all failed jobs
php artisan queue:retry all
```

## Customization

### Change Notification Time

Edit `GenerateWeeklyPlans.php`:

```php
// Send at 09:00 instead of 08:00
$notificationTime = now()->setHour(9)->setMinute(0)->setSecond(0);
```

### Different Times per User (Future)

```php
// In user_profiles table
$preferredNotificationTime = $user->profile->notification_time ?? '08:00';

$notificationTime = Carbon::parse($preferredNotificationTime);
```

## Benefits

✅ **User Engagement**: Motivational message at perfect time
✅ **No Sleep Disruption**: Not sent at midnight
✅ **Bilingual**: Respects user's language
✅ **Professional**: Well-crafted, encouraging messages
✅ **Actionable**: Deep link to Plans screen
✅ **Delayed Queue**: Doesn't block command execution

---

**Users get motivated at the perfect time!** 🎯

