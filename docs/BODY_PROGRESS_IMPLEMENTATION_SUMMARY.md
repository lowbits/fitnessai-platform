# Body Progress Tracking - Implementation Summary

## Änderungen 2026-01-04

### 1. Konsistente Namenskonventionen

Alle Felder verwenden jetzt `_kg` und `_cm` Suffixe für bessere API-Konsistenz mit dem UserProfile:

**Vorher:**
- `weight` → **Jetzt:** `weight_kg`
- `muscle_mass` → **Jetzt:** `muscle_mass_kg`
- `waist_circumference` → **Jetzt:** `waist_circumference_cm`
- `chest_circumference` → **Jetzt:** `chest_circumference_cm`
- `hip_circumference` → **Jetzt:** `hip_circumference_cm`
- `arm_circumference` → **Jetzt:** `arm_circumference_cm`
- `thigh_circumference` → **Jetzt:** `thigh_circumference_cm`

### 2. Aktuelles Gewicht aus Body Progress

Die `/api/v2/auth/me` Endpoint gibt jetzt das aktuelle Gewicht aus dem neuesten Body Progress Eintrag zurück, falls vorhanden. Andernfalls wird das Gewicht aus dem UserProfile verwendet.

**User Model - Neue Methode:**
```php
public function getCurrentWeight(): ?float
{
    $latestProgress = $this->bodyProgress()
        ->orderBy('recorded_at', 'desc')
        ->first();

    return $latestProgress?->weight_kg ?? $this->profile?->weight_kg;
}
```

**AuthController - Aktualisiert:**
```php
'weight' => $user->getCurrentWeight(), // Statt: $user->profile?->weight_kg
```

### 3. Vorteile

✅ **Konsistente API**: Alle Gewichts- und Größenangaben verwenden dieselben Suffixe
✅ **Aktuelles Gewicht**: User sehen immer ihr zuletzt getracktes Gewicht
✅ **Flexibel**: Wenn kein Body Progress existiert, wird das Profil-Gewicht verwendet
✅ **Rückwärtskompatibel**: UserProfile behält `weight_kg` Feld

### 4. Beispiel API Response

**Vorher** (Onboarding Gewicht):
```json
{
  "user": {
    "profile": {
      "weight": 75.0  // Aus UserProfile
    }
  }
}
```

**Nachher** (Nach Body Progress Tracking):
```json
{
  "user": {
    "profile": {
      "weight": 76.5  // Aus neuestem BodyProgress Eintrag
    }
  }
}
```

### 5. Migration Summary

**Database Table: `body_progress`**
```sql
- weight_kg (DECIMAL 5,2) NOT NULL
- body_fat_percentage (DECIMAL 5,2) NULL
- muscle_mass_kg (DECIMAL 5,2) NULL
- waist_circumference_cm (DECIMAL 5,2) NULL
- chest_circumference_cm (DECIMAL 5,2) NULL
- hip_circumference_cm (DECIMAL 5,2) NULL
- arm_circumference_cm (DECIMAL 5,2) NULL
- thigh_circumference_cm (DECIMAL 5,2) NULL
- notes (TEXT) NULL
- recorded_at (TIMESTAMP) NOT NULL
```

### 6. Betroffene Dateien

**Backend:**
- ✅ `database/migrations/2026_01_04_100000_create_body_progress_table.php`
- ✅ `app/Models/BodyProgress.php`
- ✅ `app/Models/User.php` (neue `getCurrentWeight()` Methode)
- ✅ `app/Http/Controllers/Api/V2/BodyProgressController.php`
- ✅ `app/Http/Controllers/Api/V2/AuthController.php`
- ✅ `database/factories/BodyProgressFactory.php`

**Tests:**
- ✅ `tests/Feature/BodyProgressTrackingTest.php` (15 Tests)

**Dokumentation:**
- ✅ `docs/BODY_PROGRESS_TRACKING.md`

### 7. Breaking Changes

⚠️ **API Field Namen haben sich geändert:**

**Mobile App muss aktualisiert werden:**
```javascript
// ALT ❌
{ weight: 75.5 }

// NEU ✅
{ weight_kg: 75.5 }
```

Alle circumference Felder müssen ebenfalls aktualisiert werden (siehe Dokumentation).

### 8. Testing

```bash
# Migration ausführen
php artisan migrate

# Tests ausführen
php artisan test --filter=BodyProgressTrackingTest

# Alle Tests sollten grün sein (15/15)
```

### 9. Next Steps für Frontend/Mobile

1. **API Calls aktualisieren**: Verwenden Sie saubere Feldnamen ohne Suffixe (`weight`, nicht `weight_kg`)
2. **User Profil**: Das `weight` Feld wird automatisch das neueste Tracking anzeigen
3. **UI anpassen**: Nutzer darauf hinweisen, dass ihr Gewicht jetzt getrackt werden kann
4. **Charts**: Body Progress Historie für Gewichtsverlauf-Charts verwenden

### 10. Zukünftige Erweiterungen

**Imperial/Metric Support (Geplant):**
```javascript
// User Preference
user.measurement = 'imperial' | 'metric'

// API Response automatisch konvertiert
// Metric User (weight_kg in DB: 75.5)
{ "weight": 75.5 }

// Imperial User (weight_kg in DB: 75.5)
{ "weight": 166.45 }  // Automatically converted to lbs

// Backend bleibt unverändert - Konvertierung in Accessors
```

### 11. Dokumentation

Vollständige API-Dokumentation siehe: `docs/BODY_PROGRESS_TRACKING.md`

---

**Implementiert am**: 2026-01-04  
**Version**: 2.0  
**Status**: ✅ Ready for Production

