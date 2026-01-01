# Workout Plan Commands - Quick Reference

## Retry Failed Workout Plans

### Basic Usage

```bash
# Retry all failed workout plans
php artisan workouts:retry-failed

# Retry for specific plan
php artisan workouts:retry-failed --plan=123

# Retry for specific user
php artisan workouts:retry-failed --user=456

# Reset status only (don't dispatch jobs)
php artisan workouts:retry-failed --reset
```

### Options

| Option | Description | Example |
|--------|-------------|---------|
| `--plan=ID` | Filter by plan ID | `--plan=123` |
| `--user=ID` | Filter by user ID | `--user=456` |
| `--reset` | Only reset status, don't dispatch jobs | `--reset` |

### Examples

#### Scenario 1: OpenAI API was down

```bash
php artisan workouts:retry-failed
```

#### Scenario 2: Specific user has issues

```bash
php artisan workouts:retry-failed --user=789
```

#### Scenario 3: Manual job processing

```bash
# Reset status first
php artisan workouts:retry-failed --reset

# Then process queue manually
php artisan queue:work --once
```

## Check Workout Plan Status

### Via Tinker

```bash
php artisan tinker
```

```php
// Count by status
WorkoutPlan::where('status', 'failed')->count();
WorkoutPlan::where('status', 'pending')->count();
WorkoutPlan::where('status', 'generated')->count();

// List failed workouts
WorkoutPlan::where('status', 'failed')
    ->with('plan.user')
    ->get()
    ->each(fn($w) => dump([
        'id' => $w->id,
        'plan_id' => $w->plan_id,
        'user' => $w->plan->user->email,
        'day' => $w->day_number,
    ]));

// Find specific plan's failed workouts
WorkoutPlan::where('plan_id', 123)
    ->where('status', 'failed')
    ->get();
```

### Via Database

```sql
-- Count failed workouts by plan
SELECT plan_id, COUNT(*) as failed_count
FROM workout_plans
WHERE status = 'failed'
GROUP BY plan_id;

-- List all failed workouts with user info
SELECT 
    wp.id,
    wp.plan_id,
    wp.day_number,
    wp.workout_name,
    wp.created_at,
    u.email as user_email
FROM workout_plans wp
JOIN plans p ON wp.plan_id = p.id
JOIN users u ON p.user_id = u.id
WHERE wp.status = 'failed';
```

## Troubleshooting

### Failed workouts stuck in pending

```bash
# Check how long they've been pending
php artisan tinker
>>> WorkoutPlan::where('status', 'pending')
    ->where('created_at', '<', now()->subHours(1))
    ->count();

# Manually dispatch jobs
>>> $plan = Plan::find(123);
>>> \App\Jobs\GenerateUserWorkoutPlan::dispatch($plan->user, $plan);
```

### Queue not processing

```bash
# Check if queue worker is running
ps aux | grep "queue:work"

# Start queue worker
php artisan queue:work

# Process one job at a time
php artisan queue:work --once

# Check failed jobs
php artisan queue:failed
```

### OpenAI API errors

```bash
# Check logs
tail -f storage/logs/laravel.log | grep -i "openai\|workout"

# Test OpenAI connection
php artisan tinker
>>> $client = OpenAI::client(config('services.openai.api_key'));
>>> $response = $client->chat()->create([
    'model' => 'gpt-4',
    'messages' => [['role' => 'user', 'content' => 'test']]
]);
```

## Monitoring

### Real-time logs

```bash
# All logs
tail -f storage/logs/laravel.log

# Only workout generation
tail -f storage/logs/laravel.log | grep -i workout

# Only errors
tail -f storage/logs/laravel.log | grep -i error
```

### Check queue status

```bash
# If using database queue
php artisan queue:monitor

# If using Redis + Horizon
php artisan horizon
```

## Automation

### Cron job for auto-retry

Add to `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('workouts:retry-failed --reset')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->onOneServer();
```

Then add to crontab:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Performance Tips

1. **Use Redis for queues** (faster than database)
   ```bash
   # .env
   QUEUE_CONNECTION=redis
   ```

2. **Run multiple queue workers**
   ```bash
   # Start 3 workers
   php artisan queue:work &
   php artisan queue:work &
   php artisan queue:work &
   ```

3. **Use Supervisor** (production)
   ```ini
   [program:laravel-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
   autostart=true
   autorestart=true
   numprocs=3
   user=www-data
   redirect_stderr=true
   stdout_logfile=/path/to/worker.log
   ```

## Related Documentation

- [Workout Tracking Implementation](WORKOUT_TRACKING_IMPLEMENTATION.md)
- [Retry Failed Workout Plans (Full Docs)](RETRY_FAILED_WORKOUT_PLANS.md)
- [Workout Plan Generation](WORKOUT_PLAN_GENERATION.md)

