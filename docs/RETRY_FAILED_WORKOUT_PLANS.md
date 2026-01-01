# Retry Failed Workout Plans Command

## Übersicht

Der `workouts:retry-failed` Artisan-Befehl ermöglicht es, fehlgeschlagene Workout-Plan-Generierungen erneut zu versuchen. Dies ist nützlich, wenn die AI-Generierung aufgrund von API-Fehlern, Timeouts oder anderen temporären Problemen fehlgeschlagen ist.

## Installation

Das Kommando ist bereits in Laravel registriert und kann direkt verwendet werden.

## Verwendung

### Alle fehlgeschlagenen Workout-Pläne erneut versuchen

```bash
php artisan workouts:retry-failed
```

Das Kommando wird:
1. Alle WorkoutPlans mit `status = 'failed'` finden
2. Sie nach Plan-IDs gruppieren
3. Sie um Bestätigung bitten
4. Den Status auf `pending` zurücksetzen
5. Die `GenerateUserWorkoutPlan` Jobs erneut dispatchen

### Optionen

#### Nur für einen bestimmten Plan

```bash
php artisan workouts:retry-failed --plan=123
```

Verarbeitet nur fehlgeschlagene Workouts für Plan mit ID 123.

#### Nur für einen bestimmten Benutzer

```bash
php artisan workouts:retry-failed --user=456
```

Verarbeitet nur fehlgeschlagene Workouts für Benutzer mit ID 456.

#### Mehrere Filter kombinieren

```bash
php artisan workouts:retry-failed --user=456 --plan=123
```

#### Nur Status zurücksetzen (ohne Job zu dispatchen)

```bash
php artisan workouts:retry-failed --reset
```

Setzt den Status von `failed` auf `pending` zurück, ohne die Generierungs-Jobs erneut zu dispatchen. Nützlich, wenn Sie die Jobs manuell ausführen möchten.

## Beispiele

### Szenario 1: OpenAI API hatte einen Ausfall

Während der Workout-Plan-Generierung hatte die OpenAI API einen temporären Ausfall und mehrere Pläne schlugen fehl.

```bash
# Alle fehlgeschlagenen Pläne erneut versuchen
php artisan workouts:retry-failed

# Ausgabe:
# Looking for failed workout plans...
# Found 15 failed workout plan(s).
# Plans affected: 5
# Do you want to retry generating these workout plans? (yes/no) [yes]:
# > yes
#   ✓ Dispatched job for Plan #123 (User #1) - 3 failed workout(s)
#   ✓ Dispatched job for Plan #124 (User #2) - 2 failed workout(s)
#   ✓ Dispatched job for Plan #125 (User #1) - 5 failed workout(s)
#   ✓ Dispatched job for Plan #126 (User #3) - 3 failed workout(s)
#   ✓ Dispatched job for Plan #127 (User #4) - 2 failed workout(s)
# 
# Successfully dispatched 5 generation job(s).
# Jobs are queued and will be processed by your queue worker.
```

### Szenario 2: Ein spezifischer Benutzer hatte Probleme

Ein bestimmter Benutzer meldete, dass sein Workout-Plan unvollständig ist.

```bash
# Nur für diesen Benutzer
php artisan workouts:retry-failed --user=789

# Ausgabe:
# Looking for failed workout plans...
# Found 3 failed workout plan(s).
# Plans affected: 1
# Do you want to retry generating these workout plans? (yes/no) [yes]:
# > yes
#   ✓ Dispatched job for Plan #150 (User #789) - 3 failed workout(s)
# 
# Successfully dispatched 1 generation job(s).
# Jobs are queued and will be processed by your queue worker.
```

### Szenario 3: Manuelles Re-Processing

Sie möchten die Jobs manuell über `php artisan queue:work` verarbeiten.

```bash
# Zuerst Status zurücksetzen
php artisan workouts:retry-failed --reset

# Ausgabe:
# Looking for failed workout plans...
# Found 8 failed workout plan(s).
# Resetting status to pending...
#   - WorkoutPlan #100 (Plan #10, Day 1) reset to pending
#   - WorkoutPlan #101 (Plan #10, Day 2) reset to pending
#   - WorkoutPlan #102 (Plan #10, Day 3) reset to pending
#   - WorkoutPlan #103 (Plan #11, Day 1) reset to pending
#   - WorkoutPlan #104 (Plan #11, Day 5) reset to pending
#   - WorkoutPlan #105 (Plan #12, Day 2) reset to pending
#   - WorkoutPlan #106 (Plan #12, Day 4) reset to pending
#   - WorkoutPlan #107 (Plan #13, Day 1) reset to pending
# Status reset complete. Run workout generation job to retry.

# Dann manuell den Plan verarbeiten
php artisan queue:work --once
```

### Szenario 4: Keine fehlgeschlagenen Pläne

```bash
php artisan workouts:retry-failed

# Ausgabe:
# Looking for failed workout plans...
# No failed workout plans found.
```

## Wie es funktioniert

### Workflow

1. **Suche nach fehlgeschlagenen Workouts**
   ```php
   WorkoutPlan::where('status', 'failed')->get()
   ```

2. **Gruppierung nach Plan-ID**
   - Vermeidet mehrfache Job-Dispatches für denselben Plan
   - Ein Plan kann mehrere fehlgeschlagene Workout-Tage haben

3. **Status-Reset**
   ```php
   WorkoutPlan::where('plan_id', $planId)
       ->where('status', 'failed')
       ->update(['status' => 'pending']);
   ```

4. **Job-Dispatch**
   ```php
   GenerateUserWorkoutPlan::dispatch($user, $plan);
   ```

5. **Der Job verarbeitet dann:**
   - Überspringt bereits generierte Workouts (`status = 'generated'`)
   - Verarbeitet nur `pending` Workouts
   - Setzt bei Erfolg auf `generated`
   - Setzt bei Fehler zurück auf `failed`

## Status-Flow

```
pending → [Job läuft] → generated (Erfolg)
                     └→ failed (Fehler)

failed → [Command] → pending → [Job läuft erneut] → generated
```

## Integration mit Queue System

Das Kommando funktioniert mit allen Laravel Queue-Treibern:

- **sync** - Sofortige Ausführung (Development)
- **database** - Queue in Datenbank (Production)
- **redis** - Queue in Redis (Production, empfohlen)
- **sqs** - AWS SQS (Cloud)

Stellen Sie sicher, dass Ihr Queue-Worker läuft:

```bash
# Development
php artisan queue:work

# Production (mit Supervisor)
php artisan queue:work --tries=3 --timeout=300
```

## Monitoring & Logging

Das Kommando loggt alle Aktionen. Überwachen Sie die Logs:

```bash
tail -f storage/logs/laravel.log
```

Sie können auch Laravel Horizon verwenden (wenn Redis-Queue):

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

## Fehlerbehebung

### Problem: "No failed workout plans found" aber User meldet Probleme

```bash
# Prüfen Sie den tatsächlichen Status
php artisan tinker
>>> WorkoutPlan::where('status', 'failed')->count()
>>> WorkoutPlan::whereNull('status')->count()
>>> WorkoutPlan::where('status', 'pending')->where('created_at', '<', now()->subHours(2))->count()
```

### Problem: Jobs werden nicht verarbeitet

```bash
# Prüfen Sie, ob Queue-Worker läuft
ps aux | grep "queue:work"

# Prüfen Sie die Queue-Verbindung
php artisan queue:monitor

# Manuell einen Job verarbeiten
php artisan queue:work --once
```

### Problem: Jobs schlagen immer noch fehl

```bash
# Logs prüfen
tail -100 storage/logs/laravel.log

# Spezifischer Plan debuggen
php artisan tinker
>>> $plan = Plan::find(123);
>>> GenerateUserWorkoutPlan::dispatch($plan->user, $plan);
```

## Cron Job (Optional)

Sie können das Kommando automatisch ausführen lassen:

```bash
# In schedule() Methode von routes/console.php
Schedule::command('workouts:retry-failed --reset')
    ->dailyAt('03:00')
    ->withoutOverlapping();
```

Dann muss der Queue-Worker die Jobs verarbeiten:

```bash
# Crontab
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## API Alternative

Wenn Sie dies programmatisch machen möchten:

```php
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\WorkoutPlan;

// Reset und retry
$failedWorkouts = WorkoutPlan::where('status', 'failed')
    ->where('plan_id', $planId)
    ->get();

$failedWorkouts->each(fn($w) => $w->update(['status' => 'pending']));

GenerateUserWorkoutPlan::dispatch($user, $plan);
```

## Tests

Tests befinden sich in `tests/Feature/RetryFailedWorkoutPlansTest.php`:

```bash
php artisan test tests/Feature/RetryFailedWorkoutPlansTest.php
```

Folgende Szenarien werden getestet:
- ✅ Retry aller fehlgeschlagenen Pläne
- ✅ Filter nach Plan-ID
- ✅ Filter nach User-ID
- ✅ Reset-Only-Modus
- ✅ Keine fehlgeschlagenen Pläne
- ✅ Benutzer-Abbruch

## Sicherheit

- **Authorization**: Kommando sollte nur von Admins ausgeführt werden
- **Rate Limiting**: Bei vielen fehlgeschlagenen Jobs OpenAI Rate Limits beachten
- **Monitoring**: Logs und Failed Jobs überwachen

## Performance

- **Batch Processing**: Jobs werden gruppiert nach Plan-ID
- **Queue**: Asynchrone Verarbeitung via Queue
- **Memory**: Große Datenmengen werden paginiert

## Related Commands

- `php artisan queue:work` - Queue-Worker starten
- `php artisan queue:failed` - Fehlgeschlagene Queue-Jobs anzeigen
- `php artisan queue:retry all` - Fehlgeschlagene Queue-Jobs erneut versuchen

