# Smart Meal Reminders - MVP (No Spam!)

## Problem
Wir wollen nicht 4x am Tag Notifications senden (Breakfast, Lunch, Snack, Dinner) - das ist Spam!

## Lösung
**Nur 1 Meal Reminder pro Tag** zur wichtigsten Mahlzeit.

## MVP Logik

### Alle User
- Bekommen **1 Meal Reminder** um **12:00 Uhr** (Lunch)
- Lunch ist die wichtigste Mahlzeit für Fitness/Ernährung
- Keine Spam-Notifications

## Implementation

### Command
```php
php artisan notifications:meal-reminders
```

Läuft täglich um 12:00:
```php
Schedule::command('notifications:meal-reminders')
    ->dailyAt('12:00');
```

### Logik
```php
private function shouldSendReminderNow(User $user, int $currentHour): bool
{
    // Default: Send meal reminder at 12:00 (lunch time)
    $reminderHour = 12;
    
    if ($currentHour === $reminderHour) {
        $this->line("User {$user->id}: Sending meal reminder at 12:00");
        return true;
    }
    
    return false;
}
```

### Welche Mahlzeit wird gesendet?
1. **Primär**: Lunch (meal_type = 'lunch')
2. **Fallback**: Erste verfügbare Mahlzeit des Tages

```php
// Get lunch
$meal = $todayMealPlan->meals()
    ->where('meal_type', 'lunch')
    ->first();

if (!$meal) {
    // Fallback to any meal
    $meal = $todayMealPlan->meals()->first();
}
```

## Warum nur 1 Reminder?

### ❌ Problem mit 4 Reminders:
```
07:30 - Breakfast reminder
12:00 - Lunch reminder
15:30 - Snack reminder
18:30 - Dinner reminder
```
= **4 Notifications pro Tag** = Spam = User deaktiviert Notifications

### ✅ Lösung mit 1 Reminder:
```
12:00 - Meal reminder (Lunch)
```
= **1 Notification pro Tag** = Kein Spam = User behält Notifications aktiv

## Beispiel Output

```
Starting meal reminder notifications for hour 12:00...
User 1: Sending meal reminder at 12:00
✅ Meal reminder sent to user 1 (john@example.com) - lunch: Chicken & Rice Bowl
User 2: Sending meal reminder at 12:00
✅ Meal reminder sent to user 2 (jane@example.com) - lunch: Quinoa Salad
✅ Meal reminders sent: 2
⏭️  Skipped (wrong time): 0
Done!
```

## Benefits

✅ **Kein Spam** - Nur 1 Notification pro Tag
✅ **Best Time** - 12:00 Uhr (wichtigste Mahlzeit)
✅ **Simple** - Keine komplexe Logik nötig
✅ **Fokussiert** - Lunch ist wichtigste Meal für Fitness
✅ **User-Friendly** - Nicht nervig

## Future Enhancements

Wenn User Feedback kommt, könnte man:
- User-Setting: Welche Mahlzeit bevorzugt
- Personalisierung basiert auf Workout-Zeit:
  - Morgen-Sportler → Breakfast
  - Abend-Sportler → Dinner
- Aber für MVP: **Keep it simple!**

## Testing

```bash
# Test
php artisan notifications:meal-reminders

# Zeigt welche User Reminder bekommen
```

## Compared to Workout Reminders

| Feature | Workout Reminders | Meal Reminders |
|---------|------------------|----------------|
| Frequency | 1x pro Tag | 1x pro Tag |
| Time | Personalisiert (learned) | Fix 12:00 |
| Learning | ✅ Von Trackings | ❌ Kein Tracking |
| Spam | ❌ Nein | ❌ Nein |

---

**Fokus auf Lunch, kein Spam, simple MVP!** 🥗

