# ✅ FINAL: Daily Plan Generation mit individuellen User-Zeiten

## Was wurde geändert:

### ❌ Alt (für alle gleich):
```
Jeden Mittwoch um 00:00 → Generiere für ALLE User
```

### ✅ Neu (individuell pro User):
```
Jeden Tag um 00:00 → Prüfe für JEDEN User:
  - Ist heute sein "Wochenmitte" Tag?
  - Basierend auf wann SEIN Plan gestartet ist
  - Nur dann generieren
```

## Neue Logik:

### 1. Command läuft **täglich**
```php
Schedule::command('plans:generate-weekly')
    ->dailyAt('00:00'); // Täglich, nicht wöchentlich!
```

### 2. Individueller "Generation Day" pro User

```php
private function isUserGenerationDay($plan): bool
{
    $planStartDayOfWeek = Carbon::parse($plan->start_date)->dayOfWeek;
    $midWeekDay = ($planStartDayOfWeek + 3) % 7;
    $todayDayOfWeek = now()->dayOfWeek;
    
    return $todayDayOfWeek === $midWeekDay;
}
```

### 3. Beispiele:

**User A:**
```
Plan started: Monday, Jan 6
→ Day of week: 1 (Monday)
→ Mid-week: (1 + 3) % 7 = 4 (Thursday)
→ Generation Day: Every Thursday
```

**User B:**
```
Plan started: Friday, Jan 10
→ Day of week: 5 (Friday)
→ Mid-week: (5 + 3) % 7 = 1 (Monday)
→ Generation Day: Every Monday
```

**User C:**
```
Plan started: Wednesday, Jan 8
→ Day of week: 3 (Wednesday)
→ Mid-week: (3 + 3) % 7 = 6 (Saturday)
→ Generation Day: Every Saturday
```

## Command Output:

```bash
# Running on Thursday, Jan 9
php artisan plans:generate-weekly
```

**Output:**
```
Starting weekly plan generation...
Found 5 user(s) with active subscriptions

✅ Queued generation for user 1 (john@example.com)
   Generation Day: Thursday
   Start: 2026-01-10 | End: 2026-01-16 | Days: 7
   📱 Notification scheduled for: 2026-01-09 08:00

⏭️  Skipped user 2 (jane@example.com) - not their generation day

✅ Queued generation for user 3 (bob@example.com)
   Generation Day: Thursday
   Start: 2026-01-10 | End: 2026-01-16 | Days: 7
   📱 Notification scheduled for: 2026-01-09 08:00

⏭️  Skipped user 4 (alice@example.com) - not their generation day

⏭️  Skipped user 5 (tom@example.com) - already has plans for next week

Summary:
+-----------+-------+
| Generated | 2     |
| Skipped   | 3     |
| Failed    | 0     |
| Total     | 5     |
+-----------+-------+
```

## 📱 Notification System

### Timing Strategy

**Problem:** Command läuft um 00:00, aber niemand will um Mitternacht eine Push Notification bekommen!

**Lösung:** Delayed Notifications mit Queue

```php
// Generation um 00:00
GenerateUserWorkoutPlan::dispatch(...);
GenerateUserMealPlan::dispatch(...);

// Notification um 08:00 morgens
$notificationTime = now()->setHour(8)->setMinute(0);
$delay = now()->diffInSeconds($notificationTime);

$user->notify(
    (new WeeklyPlansGeneratedNotification(...))->delay($delay)
);
```

### Notification Messages

**Englisch:**
- Title: 🎯 Your Week is Ready!
- Body: We've prepared your next 7 days of workouts and meals. Let's crush it! 💪

**Deutsch:**
- Title: 🎯 Deine Woche ist bereit!
- Body: Wir haben die nächsten 7 Tage für dich geplant. Lass uns loslegen! 💪

### Timeline Example

```
00:00 - Command runs
        ├─ Generate workout plans
        ├─ Generate meal plans
        └─ Queue notification with delay

08:00 - User receives notification
        "🎯 Your Week is Ready!"
```

## Vorteile:

✅ **Individuell**: Jeder User bekommt Pläne basierend auf seinem Rhythmus
✅ **Fair verteilt**: Nicht alle Generierungen am gleichen Tag (Last-Balance)
✅ **Flexibel**: Später kann User selbst seinen Tag wählen
✅ **Automatisch**: Kein manuelles Eingreifen nötig
✅ **User Engagement**: Notification um 08:00 statt 00:00 (keine Nacht-Störung!)
✅ **Professional**: Motivierende, bilinguale Messages

## Zukünftige Erweiterung:

### User Preference (später):

```php
// In user_profiles Tabelle:
$user->profile->preferred_generation_day; // 0-6 (Sunday-Saturday)

// Im Command:
if ($user->profile && $user->profile->preferred_generation_day !== null) {
    // Use user's preference
    $midWeekDay = $user->profile->preferred_generation_day;
} else {
    // Calculate based on plan start date
    $midWeekDay = ($planStartDayOfWeek + 3) % 7;
}
```

## Verteilung über die Woche:

```
Monday:    Users with plan start = Friday
Tuesday:   Users with plan start = Saturday
Wednesday: Users with plan start = Sunday
Thursday:  Users with plan start = Monday
Friday:    Users with plan start = Tuesday
Saturday:  Users with plan start = Wednesday
Sunday:    Users with plan start = Thursday
```

→ Last ist über die ganze Woche verteilt! 🎯

## Testing:

```bash
# Test für verschiedene Start-Tage
php artisan test tests/Feature/WeeklyPlanGenerationTest.php

# Manuell testen mit Force
php artisan plans:generate-weekly --force
```

---

**Das System ist jetzt viel intelligenter und individualisierter!** 🚀

