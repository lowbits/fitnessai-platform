# Fix: Plan Access Respects Subscription Duration

## ✅ Problem gelöst!

### Was war das Problem?

User mit Beta Subscription (30 Tage Plan) konnten nicht auf Tag 8+ zugreifen:

```json
{
  "error": "access_denied",
  "reason": "plan_expired",
  "message": "This date is beyond your plan duration...",
  "plan_end_date": "2026-01-04",
  "requested_day": 8,
  "total_days": "7"  // ❌ Hardcoded!
}
```

### Root Cause

**PlanController.php** verwendete hardcoded `config('plans.duration_days')` (7) statt den tatsächlichen `plan->duration_days` Wert aus der Datenbank.

```php
// ❌ Alt - hardcoded
$totalDays = config('plans.duration_days'); // Immer 7!

if ($dayOfPlan > $totalDays) {
    return response()->json([
        'error' => 'access_denied',
        // ...
        'total_days' => $totalDays, // Immer 7
    ], 403);
}
```

### Lösung

```php
// ✅ Neu - aus Datenbank
$totalDays = $plan->duration_days; // 7 oder 30 (je nach Subscription)

if ($dayOfPlan > $totalDays) {
    return response()->json([
        'error' => 'access_denied',
        // ...
        'plan_end_date' => $plan->end_date->format('Y-m-d'), // ✅ Auch korrigiert
        'total_days' => $totalDays, // 30 für Beta User
    ], 403);
}
```

## Flow

### Free User (7 Tage):
```
1. Onboarding: Plan wird mit duration_days=7 erstellt
2. Tag 1-7: ✅ Zugriff erlaubt
3. Tag 8: ❌ "plan_expired" (korrekt)
```

### Beta User (30 Tage):
```
1. Onboarding: Plan wird mit duration_days=7 erstellt
2. Admin: subscription:create → duration_days=30
3. Tag 1-7: ✅ Zugriff erlaubt
4. Tag 8-30: ✅ Zugriff erlaubt (jetzt gefixt!)
5. Tag 31: ❌ "plan_expired" (korrekt)
```

## Geänderte Dateien

### 1. ✅ app/Http/Controllers/Api/V2/PlanController.php

**Zeile 59-60:**
```php
// Vorher
$totalDays = config('plans.duration_days');

// Nachher
$totalDays = $plan->duration_days;
```

**Zeile 65:**
```php
// Vorher
'plan_end_date' => $plan->start_date->copy()->addDays($totalDays - 1)->format('Y-m-d'),

// Nachher
'plan_end_date' => $plan->end_date->format('Y-m-d'),
```

### 2. ✅ app/Http/Controllers/Api/V2/AuthController.php

Bereits gefixt - zeigt korrekte Subscription-Informationen:
```php
'subscription' => [
    'status' => $subscriptionStatus, // 'active' für Beta
    'tier' => $subscriptionTier, // 'beta'
    'features' => [
        'max_days_accessible' => $plan->duration_days ?? 30, // ✅
    ],
],
```

## API Response (Jetzt korrekt)

### Beta User greift auf Tag 8 zu:
```json
{
  "plan_id": 22,
  "plan_day": 8,
  "total_days": 30,  // ✅ Korrekt!
  "date": "2026-01-05",
  "locked": false,
  "status": "generated",
  "meals": [...],
  "workout": {...}
}
```

## Wichtig

**OnboardingController** verwendet weiterhin `config('plans.duration_days')` - das ist **korrekt**:
- Neue User starten immer mit 7 Tagen
- Wenn Subscription hinzugefügt wird → `subscription:create` Command erweitert auf 30 Tage
- API respektiert dann `plan->duration_days` aus Datenbank

## Testing

### Test Beta User Zugriff:
```bash
# User mit Beta Subscription
GET /api/v2/plans/for-date?date=2026-01-05  # Tag 8

# Vorher: 403 "plan_expired"
# Nachher: 200 mit Plan-Daten ✅
```

---

**Beta User können jetzt alle 30 Tage zugreifen!** 🎉

