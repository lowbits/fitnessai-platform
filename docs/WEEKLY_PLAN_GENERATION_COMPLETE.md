# Weekly Plan Generation - Complete Implementation

## ✅ Was wurde implementiert

### 1. Command: `plans:generate-weekly`

Generiert automatisch die nächsten 7 Tage für alle User mit aktiven Subscriptions basierend auf ihrem individuellen Plan-Start-Datum.

```bash
php artisan plans:generate-weekly
```

**Features:**
- ✅ Läuft **täglich** (nicht nur Mittwoch!)
- ✅ Prüft für jeden User individuell: Ist heute sein "Wochenmitte" Tag?
- ✅ Wochenmitte = 3-4 Tage nach Plan-Start-Datum
- ✅ Findet User mit aktiven Subscriptions
- ✅ Prüft ob neue Plans nötig sind (< 7 Tage voraus)
- ✅ Generiert nächste 7 Tage
- ✅ Respektiert Plan-End-Date
- ✅ Queued Jobs für async Verarbeitung

### 2. Scheduled Execution

**Schedule:** Täglich um 00:00 Uhr
```php
Schedule::command('plans:generate-weekly')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->runInBackground();
```

### 3. Logic Flow

```
1. Command läuft täglich um 00:00

2. Finde User mit:
   ├─ Aktiver Subscription (status = 'active')
   └─ Aktivem Plan (status = 'active')

3. Für jeden User:
   ├─ Berechne: Wochenmitte basierend auf plan.start_date
   │  └─ start_date = Montag → Wochenmitte = Donnerstag
   │  └─ start_date = Mittwoch → Wochenmitte = Samstag
   │  └─ Formel: (start_day_of_week + 3) % 7
   ├─ Prüfe: Ist heute der Wochenmitte-Tag?
   │  ├─ Ja → Weiter prüfen
   │  └─ Nein → Skip
   ├─ Prüfe: Letzte generierte Workout-Date
   ├─ Berechne: Wie viele Tage in Zukunft?
   ├─ Wenn < 7 Tage → Generiere mehr
   └─ Wenn >= 7 Tage → Skip

4. Generiere:
   ├─ Start: Tag nach letztem Workout
   ├─ End: Start + 7 Tage (oder Plan End Date)
   ├─ Queue: GenerateUserWorkoutPlan Job
   └─ Queue: GenerateUserMealPlan Job
```

## Individual Generation Days

Jeder User hat seinen eigenen "Generation Day" basierend auf wann sein Plan gestartet ist:

| Plan Started | Generation Day | Days After Start |
|-------------|----------------|------------------|
| Monday      | Thursday       | +3 days          |
| Tuesday     | Friday         | +3 days          |
| Wednesday   | Saturday       | +3 days          |
| Thursday    | Sunday         | +3 days          |
| Friday      | Monday         | +3 days          |
| Saturday    | Tuesday        | +3 days          |
| Sunday      | Wednesday      | +3 days          |

**Beispiel:**
```
User A: Plan started Monday, Jan 6
→ Generation Day: Thursday
→ Generiert jeden Donnerstag die nächste Woche

User B: Plan started Friday, Jan 10
→ Generation Day: Monday
→ Generiert jeden Montag die nächste Woche
```

## Usage Examples

### Example 1: Run daily (automatic)
```bash
# Today is Thursday, January 9, 2026
php artisan plans:generate-weekly
```

**Output:**
```
Starting weekly plan generation...
Found 5 user(s) with active subscriptions
✅ Queued generation for user 1 (john@example.com)
   Generation Day: Thursday
   Start: 2026-01-10 | End: 2026-01-16 | Days: 7
⏭️  Skipped user 2 (jane@example.com) - not their generation day
✅ Queued generation for user 3 (bob@example.com)
   Generation Day: Thursday
   Start: 2026-01-10 | End: 2026-01-16 | Days: 7
⏭️  Skipped user 4 (alice@example.com) - not their generation day
⏭️  Skipped user 5 (tom@example.com) - already has plans for next week

Summary:
+-----------+-------+
| Status    | Count |
+-----------+-------+
| Generated | 2     |
| Skipped   | 3     |
| Failed    | 0     |
| Total     | 5     |
+-----------+-------+
```

### Example 2: Force run on any day
```bash
# Today is Monday
php artisan plans:generate-weekly --force
```

**Output:**
```
Starting weekly plan generation...
Found 3 user(s) with active subscriptions
...
```

### Example 3: No users found
```bash
php artisan plans:generate-weekly
```

**Output:**
```
Starting weekly plan generation...
No users with active subscriptions and plans found.
```

## Skip Logic

User wird übersprungen wenn:
- ✅ Bereits >= 7 Tage an Workouts in der Zukunft hat
- ✅ Keine aktive Subscription
- ✅ Kein aktiver Plan

**Example:**
```
Heute: 2026-01-08
Letztes Workout: 2026-01-20 (12 Tage in Zukunft)
→ Skip (hat schon genug Pläne)

Heute: 2026-01-08
Letztes Workout: 2026-01-12 (4 Tage in Zukunft)
→ Generate (braucht mehr Pläne)
```

## Plan End Date Respect

Generierung stoppt am Plan End Date:

```
Plan End Date: 2026-01-15
Letztes Workout: 2026-01-10
→ Generiere nur: 2026-01-11 bis 2026-01-15 (5 Tage, nicht 7)
```

## Testing

### Run all tests
```bash
php artisan test tests/Feature/WeeklyPlanGenerationTest.php
```

### Individual tests
```bash
# Test 1: Only runs mid-week
php artisan test --filter="command only runs on wednesday or thursday"

# Test 2: Force flag works
php artisan test --filter="command runs with force flag on any day"

# Test 3: Generates for subscribed users
php artisan test --filter="generates plans for users with active subscription"

# Test 4: Skips non-subscribed
php artisan test --filter="skips users without active subscription"

# Test 5: Correct start date calculation
php artisan test --filter="calculates correct start date"

# Test 6: Skip users with enough plans
php artisan test --filter="skips user who already has 7+ days"

# Test 7: Respects end date
php artisan test --filter="respects plan end date"
```

## Integration with Subscription System

### Scenario 1: New Beta User
```
Day 1: User completes onboarding
       → 7-day plan created
       
Day 2: Admin adds subscription
       → Plan extended to 30 days
       → command: subscription:create user@example.com
       
Day 3: Wednesday (mid-week)
       → plans:generate-weekly runs
       → Generates days 8-14
```

### Scenario 2: Existing User
```
Week 1 Wednesday:
  - Last workout: Day 5
  - Generate: Days 6-12 (7 days)

Week 2 Wednesday:
  - Last workout: Day 12
  - Generate: Days 13-19 (7 days)

Week 3 Wednesday:
  - Last workout: Day 19
  - Generate: Days 20-26 (7 days)

Week 4 Wednesday:
  - Last workout: Day 26
  - Generate: Days 27-30 (4 days, plan ends)
  - Plan completed
```

## Command Options

```bash
# Normal run (only mid-week)
php artisan plans:generate-weekly

# Force run (any day)
php artisan plans:generate-weekly --force

# Help
php artisan plans:generate-weekly --help
```

## Monitoring & Logs

Command provides detailed output:
- User count with active subscriptions
- Generation success/skip/failure per user
- Date ranges being generated
- Summary table

Check logs for job processing:
```bash
tail -f storage/logs/laravel.log
```

## Cron Setup

The command is automatically scheduled to run daily:
```php
// routes/console.php
Schedule::command('plans:generate-weekly')
    ->dailyAt('00:00') // Every day at midnight
    ->withoutOverlapping()
    ->runInBackground();
```

**Why daily?**
- Each user has their own generation day (3-4 days after plan start)
- Command checks each user individually
- Only generates for users whose "mid-week" is today

**Crontab:**
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## What Happens Behind the Scenes

1. **Command runs** (Wednesday 00:00)
2. **Finds eligible users** (subscription + plan)
3. **Calculates dates** (next 7 days needed)
4. **Queues jobs** (async generation)
   - `GenerateUserWorkoutPlan` job
   - `GenerateUserMealPlan` job
5. **Jobs process** (AI generation)
6. **Plans created** (workoutPlans + mealPlans)
7. **User notified?** (optional: could send notification)

## Error Handling

Command continues if individual user fails:
```
✅ Queued generation for user 1
❌ Failed for user 2: Database error
✅ Queued generation for user 3

Summary:
Generated: 2
Failed: 1
```

## Next Steps

After this is working:
1. Add notification when new week is generated
2. Monitor subscription renewal
3. Create new plan after 30 days
4. Implement plan progress tracking

---

**Weekly generation is fully automated and tested!** 🚀

