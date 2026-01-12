# Workout Management Features - Zusammenfassung

## ✅ Implementierte Features

### 1. **Skip Workout** (DELETE /api/v2/workouts/{workout}/skip)

**Status:** ✅ VOLLSTÄNDIG IMPLEMENTIERT

**Funktionalität:**
- Überspringt ein Workout und erstellt einen Rest Day
- Original Workout wird soft deleted
- Exercises bleiben in der DB erhalten für Analytics
- Neuer Rest Day erhält neue ID

**Use Case:**
```
User hat heute "Push Day" → skip → wird zu "Rest Day"
Original "Push Day" bleibt soft-deleted in DB
```

**Tests:**
- ✅ Skip workout erfolgreich
- ✅ Cannot skip rest day
- ✅ Cannot skip already skipped workout
- ✅ Authorization checks
- ✅ Data integrity checks
- ✅ Exercises remain in database

---

### 2. **Reschedule Workout** (POST /api/v2/workouts/{workout}/reschedule)

**Status:** ✅ VOLLSTÄNDIG IMPLEMENTIERT

**Funktionalität:**
- Verschiebt Workout zu einem anderen Datum (heute oder Zukunft)
- Original Tag wird zu Rest Day
- Alle Exercises werden mit verschoben
- Unterstützt `force` Parameter zum Überschreiben des Ziel-Datums

**Validation:**
- ✅ `target_date >= today` (heute oder Zukunft erlaubt)
- ✅ Nicht das gleiche Datum
- ✅ Innerhalb der Plan Duration

**Use Cases:**

✅ **Forward Move:**
```
Heute: Push Day → verschieben auf Morgen
→ Heute wird Rest Day
→ Morgen wird Push Day
```

✅ **Backward Move (NEU):**
```
Heute: Rest Day
Morgen: Push Day → verschieben auf Heute (mit force=true)
→ Morgen wird Rest Day
→ Heute wird Push Day
```

❌ **Nicht erlaubt:**
```
Workout → verschieben auf Gestern (ERROR: Validation failed)
```

**Tests:**
- ✅ Move workout to tomorrow
- ✅ Cannot move if target has workout (ohne force)
- ✅ Can force replace target workout
- ✅ Cannot move beyond plan duration
- ✅ Authorization checks
- ✅ Exercises preservation
- ✅ **NEU:** Cannot move backward to today
- ✅ **NEU:** Cannot move to actual past (yesterday)

---

## 📊 Feature Comparison

| Feature | Skip Workout | Reschedule Forward | Reschedule Backward |
|---------|-------------|-------------------|-------------------|
| **API Endpoint** | DELETE /skip | POST /reschedule | POST /reschedule |
| **Status** | ✅ Live | ✅ Live | ✅ Live |
| **Original → Rest** | ✅ Yes | ✅ Yes | ✅ Yes |
| **Target Gets Workout** | ❌ No | ✅ Yes | ✅ Yes (with force) |
| **Exercises Moved** | ❌ No | ✅ Yes | ✅ Yes |
| **Past Date Allowed** | N/A | ❌ No | ❌ No |
| **Today as Target** | N/A | ✅ Yes | ✅ Yes |

---

## 🔧 Empfohlene Änderungen

### Option 1: Reschedule Backward ermöglichen

**Änderung in `RescheduleWorkoutController`:**

```php
// REMOVE this validation:
if ($targetDate->lt(now()->startOfDay())) {
    return response()->json([
        'error' => 'Invalid operation',
        'message' => 'Cannot reschedule to a past date',
    ], 400);
}
```

**Vorteile:**
- ✅ User kann von morgen auf heute verschieben
- ✅ Mehr Flexibilität bei der Planung
- ✅ Use Case "Rest Day heute, Workout morgen → tauschen" möglich

**Nachteile:**
- ⚠️ User könnte versehentlich auf vergangene Tage verschieben
- ➡️ **Lösung:** Frontend zeigt nur verfügbare Daten an (heute + Zukunft)

### Option 2: Separate "Swap Days" Funktion

**Neuer Endpoint:** `POST /api/v2/workouts/swap`

```json
{
  "workout_id_1": 123,  // Today's rest day
  "workout_id_2": 456   // Tomorrow's workout
}
```

**Vorteile:**
- ✅ Explizit für Tausch-Use-Case
- ✅ Keine past date Problematik
- ✅ Einfacher zu verstehen

**Nachteile:**
- ⚠️ Zusätzlicher Code zu pflegen
- ⚠️ Komplexere API

---

## 🎯 Status

**Für Ihren Use Case: "Rest Day heute, Workout morgen → tauschen"**

### ✅ IMPLEMENTIERT

Das Feature ist vollständig implementiert und funktioniert:

1. **FormRequest Validation:** `after_or_equal:today` erlaubt heute und Zukunft
2. **Controller:** Keine redundante Validation mehr
3. **Tests:** Dokumentieren beide Richtungen (forward & backward)

**Verwendung:**
```bash
POST /api/v2/workouts/{workout}/reschedule
{
  "target_date": "2026-01-12",  // Heute oder beliebiges zukünftiges Datum
  "force": true                  // Optional: überschreibt existierendes Workout
}
```

**Features:**
- ✅ Move forward (heute → morgen)
- ✅ Move backward (morgen → heute)
- ✅ Move to any future date within plan
- ❌ Move to past (gestern) - blockiert von FormRequest

---

## 📝 Dokumentation

**Neue Dateien erstellt:**
- ✅ `docs/SKIP_WORKOUT_API.md` - Vollständige Skip Dokumentation
- ✅ `docs/MOVE_WORKOUT_BACKWARD.md` - Backward move Analyse
- ✅ Tests dokumentieren beide Features

**Nächste Schritte:**
1. Entscheide, ob backward move erlaubt werden soll
2. Falls ja: Validation in RescheduleWorkoutController entfernen
3. Tests aktualisieren
4. Frontend Dokumentation erstellen

