# Queue Setup - Quick Reference

## ✅ Queue Structure

```
workouts  → GenerateUserWorkoutPlan (AI, slow)
nutrition → GenerateUserMealPlan (AI, slow)
default   → All Notifications (fast, high priority)
```

## Running Workers

### Development (one command):
```bash
php artisan queue:work --queue=default,workouts,nutrition
```

### Production (3 separate workers):
```bash
# Terminal 1: Notifications (fast, 4 workers)
php artisan queue:work --queue=default --sleep=1 --tries=3

# Terminal 2: Workouts (2 workers)
php artisan queue:work --queue=workouts --sleep=3 --tries=3

# Terminal 3: Nutrition (2 workers)
php artisan queue:work --queue=nutrition --sleep=3 --tries=3
```

## Why Separate Queues?

**Problem without separation:**
```
User gets notification → Waits 2 minutes (behind AI generation)
```

**Solution with separation:**
```
User gets notification → Instant! ⚡
AI generation → Runs in parallel on separate queue
```

## Commands

```bash
# Monitor queues
php artisan queue:monitor workouts nutrition default

# Failed jobs
php artisan queue:failed
php artisan queue:retry all

# Restart all workers (after code deploy)
php artisan queue:restart
```

## Testing

Tests work automatically with Queue::fake() - no changes needed!

```php
Queue::fake();
// Jobs go to correct queues automatically
Queue::assertPushed(GenerateUserWorkoutPlan::class);
```

---

**All jobs are now on optimized queues!** 🚀

