# Queue Configuration - Job Distribution

## ✅ Queue Structure

Wir verwenden 3 separate Queues für bessere Performance und Kontrolle:

### 1. **`workouts`** Queue
- **Job**: `GenerateUserWorkoutPlan`
- **Zweck**: AI-Generierung von Workout-Plänen
- **Priorität**: Normal
- **Dauer**: 30-60 Sekunden pro Job (OpenAI API calls)

### 2. **`nutrition`** Queue
- **Job**: `GenerateUserMealPlan`
- **Zweck**: AI-Generierung von Mahlzeiten-Plänen
- **Priorität**: Normal
- **Dauer**: 30-60 Sekunden pro Job (OpenAI API calls)

### 3. **`default`** Queue
- **Jobs**: Alle Notifications
  - `WeeklyPlansGeneratedNotification`
  - `DailyWorkoutReminderNotification`
  - `MealReminderNotification`
  - `RestDayReminderNotification`
  - etc.
- **Zweck**: Push Notifications über Expo
- **Priorität**: High
- **Dauer**: < 1 Sekunde pro Job

## Warum separate Queues?

### ✅ Vorteile:

1. **Isolation**: Langsame AI-Generierung blockiert nicht Notifications
2. **Priorität**: Notifications werden sofort versendet
3. **Skalierung**: Jede Queue kann individuell skaliert werden
4. **Monitoring**: Einfacher zu überwachen und debuggen
5. **Retry-Logik**: Kann pro Queue unterschiedlich konfiguriert werden

### Beispiel-Szenario:

**Ohne separate Queues (❌):**
```
Queue: default
├─ GenerateUserWorkoutPlan (60s) 🐌
├─ GenerateUserMealPlan (60s) 🐌
└─ WeeklyPlansGeneratedNotification (1s) ⏰
    → Notification wartet 120s!
```

**Mit separaten Queues (✅):**
```
Queue: workouts
└─ GenerateUserWorkoutPlan (60s) 🐌

Queue: nutrition
└─ GenerateUserMealPlan (60s) 🐌

Queue: default
└─ WeeklyPlansGeneratedNotification (1s) ⚡
    → Notification wird sofort verarbeitet!
```

## Queue Worker Commands

### Production Setup (3 separate Workers)

```bash
# Worker 1: Workouts Queue
php artisan queue:work --queue=workouts --sleep=3 --tries=3

# Worker 2: Nutrition Queue
php artisan queue:work --queue=nutrition --sleep=3 --tries=3

# Worker 3: Default Queue (Notifications - hohe Priorität)
php artisan queue:work --queue=default --sleep=1 --tries=3
```

### Supervisor Configuration

```ini
[program:laravel-queue-workouts]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/project/artisan queue:work --queue=workouts --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/project/storage/logs/queue-workouts.log

[program:laravel-queue-nutrition]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/project/artisan queue:work --queue=nutrition --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/project/storage/logs/queue-nutrition.log

[program:laravel-queue-default]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/project/artisan queue:work --queue=default --sleep=1 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/project/storage/logs/queue-default.log
```

**Notice:** Default queue hat mehr Workers (4) für schnelle Notification-Verarbeitung!

## Development

### Run all queues in one terminal (for testing):

```bash
php artisan queue:work --queue=default,workouts,nutrition
```

**Priority Order:** default → workouts → nutrition

## Monitoring

### Check Queue Status

```bash
# See jobs waiting in each queue
php artisan queue:monitor workouts nutrition default

# List failed jobs
php artisan queue:failed

# Retry failed jobs from specific queue
php artisan queue:retry --queue=workouts

# Clear failed jobs
php artisan queue:flush
```

### Queue Dashboard (Horizon - optional)

```bash
composer require laravel/horizon

php artisan horizon:install
php artisan horizon
```

## Job Configuration

### GenerateUserWorkoutPlan.php
```php
class GenerateUserWorkoutPlan implements ShouldQueue
{
    use Queueable;
    
    public function __construct(
        public User $user,
        public Plan $plan
    ) {
        $this->onQueue('workouts'); // ✅ Set queue in constructor
    }
}
```

### GenerateUserMealPlan.php
```php
class GenerateUserMealPlan implements ShouldQueue
{
    use Queueable;
    
    public function __construct(
        public User $user,
        public Plan $plan
    ) {
        $this->onQueue('nutrition'); // ✅ Set queue in constructor
    }
}
```

### All Notifications
```php
class WeeklyPlansGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    // Uses 'default' queue automatically ✅
    
    // ...
}
```

## Performance Benefits

### Scenario: Weekly Generation runs

```
00:00 - Command dispatches for 100 users:
├─ 100x GenerateUserWorkoutPlan → workouts queue
├─ 100x GenerateUserMealPlan → nutrition queue
└─ 100x WeeklyPlansGeneratedNotification (delayed) → default queue

Workers process:
├─ 2 workers on 'workouts' (50 each)
├─ 2 workers on 'nutrition' (50 each)
└─ 4 workers on 'default' (25 each)

Result:
- Notifications werden sofort verarbeitet (nicht blockiert)
- Workout/Meal Generation parallel
- Optimale Ressourcen-Nutzung
```

## Testing

### Test with specific queue:

```bash
# Only process workouts
php artisan queue:work --queue=workouts --once

# Only process notifications
php artisan queue:work --queue=default --once
```

### In Tests (already configured):

```php
Queue::fake(); // Works for all queues
Queue::assertPushed(\App\Jobs\GenerateUserWorkoutPlan::class);
```

## Environment Configuration

### .env

```env
QUEUE_CONNECTION=database  # or redis for production

# Optional: Redis for better performance
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### config/queue.php

No changes needed - queue names are set in Job classes!

## Best Practices

✅ **DO:**
- Keep notifications on default queue (fast)
- Use separate queues for long-running jobs
- Monitor queue lengths
- Set appropriate worker counts per queue

❌ **DON'T:**
- Put everything on one queue
- Mix fast and slow jobs on same queue
- Forget to start workers for all queues
- Ignore failed job alerts

---

**Queue setup is optimized for performance and user experience!** 🚀

