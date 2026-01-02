# Smart Workout Reminders - MVP Implementation

## Konzept

Einfaches, intelligentes System ohne komplexe Datenbank-Tabellen:
- **Neue User (erste 2 Wochen)**: Reminder um 18:00 Uhr
- **User ohne Trackings**: Reminder um 18:00 Uhr
- **User mit Trackings**: Reminder 1 Stunde vor letztem Workout

## Wie es funktioniert

### 1. Command läuft stündlich
```php
Schedule::command('notifications:workout-reminders')
    ->hourly()
    ->between('6:00', '23:00')
```

### 2. Für jeden User wird berechnet:

#### Fall A: Neue User (erste 14 Tage) ODER keine Trackings
```
→ Reminder-Zeit: 18:00 Uhr
→ Sendet um 18:00 Uhr
```

#### Fall B: User hat Trackings
```
Letztes Workout started_at: 19:30 Uhr
→ Workout-Zeit (Stunde): 19 Uhr
→ Reminder-Zeit: 18:00 Uhr (1h vorher)
→ Sendet um 18:00 Uhr
```

### 3. Command entscheidet pro User

```php
private function shouldSendReminderNow(User $user, int $currentHour): bool
{
    // Hole letztes Tracking (started_at!)
    $latestTracking = $user->workoutTrackings()
        ->whereNotNull('started_at')
        ->latest('started_at')
        ->first();
    
    // Neue User oder keine Trackings
    if (!$latestTracking || $isNewUser) {
        $reminderHour = 18; // Default 6 PM
    } else {
        // 1 Stunde vor letztem Workout START
        $lastWorkoutHour = Carbon::parse($latestTracking->started_at)->hour;
        $reminderHour = $lastWorkoutHour - 1;
        $reminderHour = max(6, min(23, $reminderHour)); // 6-23 Uhr
    }
    
    return $currentHour === $reminderHour;
}
```

## Beispiele

### Beispiel 1: Neuer User (Tag 3)
```
User registriert: 30.12.2025
Heute: 02.01.2026 (Tag 3)
Trackings: Keine

→ Command läuft um 18:00
→ Prüft: isNewUser = true (Tag 3 < 14 Tage)
→ Reminder-Zeit = 18:00
→ Sendet Notification ✅
```

### Beispiel 2: Erfahrener User (trainiert abends)
```
User registriert: 01.11.2025 (vor 2 Monaten)
Letztes Workout: Gestern um 20:15 Uhr GESTARTET
Heute: 02.01.2026

→ Command läuft um 19:00
→ Prüft: isNewUser = false
→ Letztes Workout-Stunde (started_at): 20 Uhr
→ Reminder-Zeit = 19:00 (1h vorher)
→ currentHour === reminderHour → true
→ Sendet Notification ✅
```

### Beispiel 3: Morgen-Sportler
```
User registriert: 01.11.2025
Letztes Workout: Gestern um 07:30 Uhr GESTARTET

→ Command läuft um 06:00
→ Prüft: Letztes Workout-Stunde (started_at): 7 Uhr
→ Reminder-Zeit = 6:00 (1h vorher, min 6)
→ Sendet Notification ✅
```

### Beispiel 4: Sehr früher Sportler
```
Letztes Workout: 05:30 Uhr GESTARTET

→ Berechnet: 5 - 1 = 4 Uhr
→ Min-Grenze: max(6, 4) = 6 Uhr
→ Sendet um 06:00 ✅
```

### Beispiel 5: Sehr späte Workouts
```
Letztes Workout: 23:15 Uhr GESTARTET

→ Berechnet: 23 - 1 = 22 Uhr
→ Sendet um 22:00 ✅
```

## Vorteile

✅ **Kein Setup nötig**: Funktioniert sofort
✅ **Lernt automatisch**: Nutzt Tracking-Daten
✅ **Kein Spam**: Jeder User bekommt nur 1 Reminder pro Tag
✅ **Personalisiert**: Basiert auf echten User-Gewohnheiten
✅ **MVP**: Keine komplexe Datenbank-Struktur
✅ **Flexibel**: Passt sich an geänderte Gewohnheiten an

## Schedule

```
06:00 - Command läuft → Sendet an User mit Reminder-Zeit 06:00
07:00 - Command läuft → Sendet an User mit Reminder-Zeit 07:00
...
18:00 - Command läuft → Sendet an User mit Reminder-Zeit 18:00 (inkl. neue User)
...
23:00 - Command läuft → Sendet an User mit Reminder-Zeit 23:00
```

## Testing

### Test für neuen User
```bash
php artisan tinker

$user = User::find(1);
$user->created_at = now()->subDays(5); // 5 Tage alt
$user->save();

# Kein Tracking vorhanden
php artisan notifications:workout-reminders

# Sollte um 18:00 Uhr senden
```

### Test für erfahrenen User
```bash
php artisan tinker

$user = User::find(1);

# Erstelle Tracking für gestern 20:00 Uhr (STARTED!)
$tracking = new WorkoutTracking([
    'user_id' => $user->id,
    'workout_plan_id' => 1,
    'started_at' => now()->subDay()->setHour(20)->setMinute(0),
]);
$tracking->save();

# Test
php artisan notifications:workout-reminders

# Sollte um 19:00 Uhr senden (1h vor 20:00)
```

## Monitoring

Command gibt detaillierte Logs:
```
Starting workout reminder notifications for hour 18:00...
User 1: Using default time 18:00 (new user or no tracking)
✅ Workout reminder sent to user 1 (john@example.com) - Push Day
User 2: Learned time 19:00 (1h before last workout at 20:00)
⏭️  Skipped (wrong time): 1
✅ Workout reminders sent: 1
✅ Rest day reminders sent: 0
⏭️  Skipped (wrong time): 1
Done!
```

## Grenzen

- **Min**: 06:00 Uhr (niemand will um 4 Uhr geweckt werden)
- **Max**: 23:00 Uhr (spätester Reminder)
- **Command läuft**: 6:00 - 23:00 Uhr stündlich

## Future Enhancements

Nach MVP könnte man hinzufügen:
- User-Settings: Reminder an/aus
- Durchschnitt der letzten 5 Workouts statt nur letztes
- Wochenende vs. Wochentag unterscheiden
- User kann eigene Zeit manuell setzen

Aber für MVP: **Keep it simple!** 🎯

---

**Das System lernt automatisch und benötigt keine User-Konfiguration!** 🚀

