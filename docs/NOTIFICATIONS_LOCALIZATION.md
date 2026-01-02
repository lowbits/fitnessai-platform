# ✅ Laravel Localization für Push Notifications

## Was wurde geändert:

Alle Notification-Texte verwenden jetzt Laravel's `__()` Lokalisierungssystem statt hardcoded strings!

## Language Files

### `/lang/en/notifications.php`
```php
return [
    'workout_reminder' => [
        'title' => 'Time for Your Workout!',
        'body' => 'Ready to crush :workout? Let\'s get moving!',
    ],
    'rest_day' => [
        'title' => 'Rest Day - Enjoy!',
        'body' => 'Your body needs rest to grow stronger...',
    ],
    'meal_reminder' => [
        'breakfast' => [
            'title' => 'Time for Breakfast!',
            'body' => 'Time to fuel your body with: :meal',
        ],
        // ... lunch, snack, dinner
    ],
];
```

### `/lang/de/notifications.php`
```php
return [
    'workout_reminder' => [
        'title' => 'Zeit für dein Training!',
        'body' => 'Bereit für :workout? Lass uns loslegen!',
    ],
    'rest_day' => [
        'title' => 'Ruhetag - Genieße ihn!',
        'body' => 'Dein Körper braucht Ruhe um stärker zu werden...',
    ],
    'meal_reminder' => [
        'breakfast' => [
            'title' => 'Zeit fürs Frühstück!',
            'body' => 'Zeit deinen Körper zu versorgen mit: :meal',
        ],
        // ... lunch, snack, dinner
    ],
];
```

## Verwendung in Notifications

### DailyWorkoutReminderNotification
```php
public function toExpo(object $notifiable): ExpoMessage
{
    return ExpoMessage::create()
        ->title('💪 ' . __('notifications.workout_reminder.title', [], $this->locale))
        ->body(__('notifications.workout_reminder.body', ['workout' => $this->workoutName], $this->locale))
        // ...
}
```

### RestDayReminderNotification
```php
public function toExpo(object $notifiable): ExpoMessage
{
    return ExpoMessage::create()
        ->title('🌟 ' . __('notifications.rest_day.title', [], $this->locale))
        ->body(__('notifications.rest_day.body', [], $this->locale))
        // ...
}
```

### MealReminderNotification
```php
public function toExpo(object $notifiable): ExpoMessage
{
    $emoji = $mealEmojis[$this->mealType] ?? '🍴';
    
    return ExpoMessage::create()
        ->title($emoji . ' ' . __("notifications.meal_reminder.{$this->mealType}.title", [], $this->locale))
        ->body(__("notifications.meal_reminder.{$this->mealType}.body", ['meal' => $this->mealName], $this->locale))
        // ...
}
```

## Vorteile

✅ **Zentralisiert**: Alle Texte an einem Ort
✅ **Wartbar**: Texte können ohne Code-Änderungen angepasst werden
✅ **Erweiterbar**: Neue Sprachen einfach hinzufügen
✅ **Laravel Standard**: Nutzt Laravel's eingebautes Lokalisierungssystem
✅ **Placeholder Support**: `:workout` und `:meal` werden automatisch ersetzt

## Neue Sprache hinzufügen

1. Erstelle `/lang/fr/notifications.php` (für Französisch)
2. Kopiere die Struktur von `en/notifications.php`
3. Übersetze alle Texte
4. Fertig! Laravel findet die Übersetzungen automatisch

## Testing

```bash
# Test mit verschiedenen Locales
php artisan tinker

# User mit deutschem Locale
$user = User::find(1);
$user->locale = 'de';
$user->save();
$user->notify(new DailyWorkoutReminderNotification('Push Day', 123));
// → Sendet deutsche Nachricht

# User mit englischem Locale
$user->locale = 'en';
$user->save();
$user->notify(new DailyWorkoutReminderNotification('Push Day', 123));
// → Sendet englische Nachricht
```

## Wie es funktioniert

1. User setzt `locale` in der Datenbank (en/de)
2. Notification liest `$notifiable->locale` (das ist der User)
3. Laravel's `__()` lädt die entsprechende Übersetzung
4. Fallback zu 'en' wenn locale nicht gesetzt oder Übersetzung fehlt

## Fallback

Wenn eine Übersetzung fehlt, verwendet Laravel automatisch die englische Version:

```php
__('notifications.workout_reminder.title', [], 'fr')  // Falls fr nicht existiert → nutzt en
```

---

**Jetzt nutzen alle Notifications das Laravel Lokalisierungssystem!** 🎯

