# Generate Weekly Plans Command - User-Specific Generation

## Übersicht

Der `plans:generate-weekly` Command wurde erweitert, um Pläne für einen spezifischen Benutzer per E-Mail zu generieren.

## Command Syntax

```bash
php artisan plans:generate-weekly [options]
```

## Optionen

### `--email=EMAIL`
Generiert Pläne für einen spezifischen Benutzer anhand der E-Mail-Adresse.

**Beispiel:**
```bash
php artisan plans:generate-weekly --email=user@example.com
```

### `--force`
Umgeht alle Validierungen und Checks:
- Generation Day Check
- Bestehende Pläne Check
- Subscription Status Check

**Beispiel:**
```bash
# Force für alle Benutzer
php artisan plans:generate-weekly --force

# Force für spezifischen Benutzer
php artisan plans:generate-weekly --email=user@example.com --force
```

## Verwendungsbeispiele

### 1. Standard-Batch-Generierung
Generiert für alle Benutzer basierend auf ihrem Generierungstag:
```bash
php artisan plans:generate-weekly
```

### 2. Spezifischer Benutzer (mit Prompts)
Generiert für einen Benutzer mit interaktiven Bestätigungen:
```bash
php artisan plans:generate-weekly --email=john@example.com
```

**Prompts die erscheinen können:**
- ⚠️ Wenn kein aktives Abo: "Generate anyway? (yes/no)"
- ⚠️ Wenn nicht Generierungstag: "Generate anyway? (yes/no)"
- ⚠️ Wenn bereits Pläne existieren: "Generate anyway? (yes/no)"

### 3. Spezifischer Benutzer (ohne Prompts)
Generiert ohne Bestätigungen mit `--force`:
```bash
php artisan plans:generate-weekly --email=john@example.com --force
```

## Output

### Erfolgreiche Generierung

```
Generating plans for user: john@example.com

📋 Generation Details:
+------------------+-------------------------+
| Property         | Value                   |
+------------------+-------------------------+
| User             | John Doe (john@...)     |
| Plan ID          | 123                     |
| Plan Start       | 2026-01-01              |
| Plan End         | 2026-01-31              |
| Generation Day   | Thursday                |
| Start Date       | 2026-01-12              |
| End Date         | 2026-01-18              |
| Days to Generate | 7                       |
| Has Subscription | Yes                     |
+------------------+-------------------------+

✅ Generation jobs dispatched successfully!
   🏋️  Workout plans queued
   🍽️  Meal plans queued

📬 Notifications:
   📧 Email sent immediately
   📱 Push notification scheduled for: 2026-01-12 08:00
```

### Fehler-Szenarien

**Benutzer nicht gefunden:**
```
❌ User not found: nonexistent@example.com
```

**Kein aktiver Plan:**
```
❌ User john@example.com has no active plan
```

**Ohne Subscription (ohne --force):**
```
⚠️  User john@example.com has no active subscription. Use --force to generate anyway.
Generate anyway? (yes/no) [no]:
```

## Validierungen

Bei Verwendung von `--email` ohne `--force` werden folgende Checks durchgeführt:

1. **Benutzer existiert** ✅ (Pflicht)
2. **Aktiver Plan existiert** ✅ (Pflicht)
3. **Aktives Abo** ⚠️ (mit Prompt)
4. **Generierungstag** ⚠️ (mit Prompt)
5. **Bereits Pläne vorhanden** ⚠️ (mit Prompt)

Mit `--force` werden alle ⚠️ Checks übersprungen.

## Anwendungsfälle

### 1. Support-Anfrage
Kunde hat Probleme, manuell nachgenerieren:
```bash
php artisan plans:generate-weekly --email=customer@example.com --force
```

### 2. Test-Account
Für einen Test-Account ohne Abo generieren:
```bash
php artisan plans:generate-weekly --email=test@example.com --force
```

### 3. Plan abgelaufen, Verlängerung
Benutzer hat Plan verlängert, sofort neu generieren:
```bash
php artisan plans:generate-weekly --email=user@example.com --force
```

### 4. Debugging
Für Entwicklung/Testing spezifisch generieren:
```bash
php artisan plans:generate-weekly --email=dev@example.com --force
```

## Unterschiede zwischen Batch und User-Specific

| Feature | Batch (ohne --email) | User-Specific (mit --email) |
|---------|---------------------|----------------------------|
| Benutzer-Auswahl | Alle mit Abo + Plan | Ein spezifischer Benutzer |
| Validierungen | Automatisch | Mit interaktiven Prompts |
| Force benötigt | Für alle Checks | Für individuelle Checks |
| Output | Zusammenfassung | Detaillierte Tabelle |
| Fehler-Handling | Weiter mit nächstem | Sofortiger Abbruch |

## Technische Details

### Generierungs-Logik

1. **Findet letztes generiertes Datum** des Benutzers
2. **Startet am nächsten Tag** nach dem letzten generierten Datum
3. **Generiert 7 Tage** (oder bis Plan-Ende)
4. **Dispatched Jobs** für Workout und Meal Plans
5. **Sendet Benachrichtigungen**:
   - Email: Sofort
   - Push: 08:00 Uhr (verzögert)

### Code-Struktur

```php
private function generateForSpecificUser(string $email, bool $force): int
{
    // 1. Benutzer finden
    // 2. Plan validieren
    // 3. Checks durchführen (mit Prompts wenn nicht --force)
    // 4. Details anzeigen
    // 5. Jobs dispatchen
    // 6. Benachrichtigungen senden
}
```

## Logging

Alle Operationen werden geloggt:

```php
Log::info('Generated plans for specific user', [
    'email' => $email,
    'plan_id' => $plan->id,
    'start_date' => $startDate,
    'end_date' => $endDate,
    'forced' => $force,
]);
```

## Best Practices

✅ **Do:**
- Verwende `--email` für manuelle Einzelgenerierungen
- Verwende `--force` für Support-Fälle
- Prüfe Output-Details vor wichtigen Operationen
- Verwende für Batch-Operationen ohne `--email`

❌ **Don't:**
- Verwende nicht `--force` für normale Batch-Runs (vermeidet doppelte Generierungen)
- Verwende nicht `--email` mit vielen Benutzern (ineffizient, nutze Batch)

## Cron Integration

Der normale Batch-Command bleibt unverändert:

```bash
# crontab
0 9 * * * cd /path/to/app && php artisan plans:generate-weekly
```

Die `--email` Option ist nur für manuelle Verwendung gedacht.


