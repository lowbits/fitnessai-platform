# Meal Replacement Feature

## Overview
Die Meal Replacement Feature ermöglicht es Benutzern, einzelne Mahlzeiten in ihrem Ernährungsplan durch Alternativen zu ersetzen. Dies kann mit oder ohne Hinweis erfolgen.

## Implementierung

### API Endpoint
```
POST /api/v2/meals/{mealId}/replace
```

**Authentifizierung:** Erforderlich (Sanctum Bearer Token)

**Request Body (optional):**
```json
{
  "hint": "Ich möchte etwas mit Lachs"
}
```

**Validierung:**
- `hint`: Optional, String, maximal 500 Zeichen

**Response (202 Accepted):**
```json
{
  "message": "Meal replacement is being generated",
  "meal_id": 123
}
```

**Fehler-Responses:**
- `401 Unauthorized`: Benutzer ist nicht authentifiziert
- `403 Forbidden`: Mahlzeit gehört nicht dem Benutzer
- `404 Not Found`: Mahlzeit existiert nicht
- `422 Unprocessable Entity`: Validierungsfehler (z.B. Hinweis zu lang)

### Architektur

#### Controller
`App\Http\Controllers\Api\V2\ReplaceMealController`

Der Controller:
1. Validiert die Eingabedaten
2. Überprüft, ob die Mahlzeit existiert
3. Verifiziert, dass die Mahlzeit dem Benutzer gehört
4. Dispatched den `ReplaceMealJob` asynchron
5. Gibt eine 202 Accepted Response zurück

#### Job
`App\Jobs\ReplaceMealJob`

Der Job wird auf der `nutrition` Queue ausgeführt und:
1. Lädt das Benutzerprofil und Ernährungsziele
2. Erstellt einen System-Prompt mit allen relevanten Informationen
3. Ruft die OpenAI Responses API auf (GPT-5-mini Modell)
4. Parst die generierte Mahlzeit aus der API-Antwort
5. Aktualisiert die existierende Mahlzeit in-place
6. Berechnet die neuen Gesamtnährwerte für den MealPlan neu

**Features:**
- Berücksichtigt Benutzerpräferenzen (Diättyp, Ziele, etc.)
- Matched ähnliche Makronährstoffe (±10% Toleranz)
- Unterstützt optionale Benutzer-Hinweise
- Mehrsprachige Unterstützung (DE/EN)
- Generiert vollständige Rezepte mit Zutaten und Anweisungen

### Unterschied mit/ohne Hinweis

**Ohne Hinweis:**
- KI generiert eine Alternative mit ähnlichen Nährwerten
- Fokus auf Abwechslung und andere Zutaten
- Automatische Empfehlung basierend auf Profil

**Mit Hinweis:**
- KI berücksichtigt den Wunsch des Benutzers
- Versucht den Hinweis mit den Nährwertzielen zu kombinieren
- Beispiele: "Ich möchte etwas mit Lachs", "Etwas Vegetarisches", "Schnelles Gericht"

### OpenAI Integration

Das Feature nutzt die OpenAI Responses API mit:
- **Modell:** gpt-5-mini
- **Tool:** `replace_meal` Function Call
- **Metadata:** user_id, meal_id, replacement flag

**System Prompt Komponenten:**
- Benutzerprofil (Alter, Gewicht, Größe, Ziele)
- Ernährungsziele (Kalorien, Makros)
- Diättyp und Präferenzen
- Aktivitätslevel
- Sprache

**Context Message:**
- Original-Mahlzeit Details
- Nährwerte der Original-Mahlzeit
- Optionaler Benutzer-Hinweis
- Anweisungen für die Ersetzung

### Tests

Vollständige Feature-Test-Suite in `tests/Feature/MealReplacementTest.php`:

1. ✅ `user can replace a meal without hint` - Ersetzung ohne Hinweis
2. ✅ `user can replace a meal with hint` - Ersetzung mit Hinweis
3. ✅ `user cannot replace meal they do not own` - Autorisierung
4. ✅ `replacing non-existent meal returns 404` - Fehlerbehandlung
5. ✅ `hint validation limits length` - Eingabevalidierung
6. ✅ `user must be authenticated to replace meal` - Authentifizierung

Alle Tests verwenden:
- `Queue::fake()` für asynchrones Testing
- Factory Pattern für Test-Daten
- Pest PHP Testing Framework

## Verwendung

### Mobile App Integration

```typescript
// Ohne Hinweis
const response = await api.post(`/meals/${mealId}/replace`);

// Mit Hinweis
const response = await api.post(`/meals/${mealId}/replace`, {
  hint: 'Ich möchte etwas mit Lachs'
});

if (response.status === 202) {
  // Zeige Lade-Indikator
  // Die Mahlzeit wird im Hintergrund generiert
  // Benutzer kann die App weiter verwenden
}
```

### Queue-Verarbeitung

Die Jobs werden auf der `nutrition` Queue verarbeitet:

```bash
# Queue Worker starten
php artisan queue:work --queue=nutrition

# Oder via Supervisor für Production
```

## Datenfluss

1. **Client** → POST Request mit optional Hint
2. **Controller** → Validierung & Authorization
3. **Job Dispatch** → Queue System (nutrition)
4. **Job Execution** → OpenAI API Call
5. **Database Update** → Meal & MealPlan Totals
6. **Client Polling** → GET /meals/{id} für aktualisierte Daten

## Best Practices

### Für Benutzer:
- Kurze, präzise Hinweise verwenden
- Spezifische Zutaten oder Zubereitungsarten erwähnen
- Diät-Einschränkungen werden automatisch berücksichtigt

### Für Entwickler:
- Job sollte idempotent sein (kann wiederholt werden)
- Logging für Debugging und Monitoring
- Fehlerbehandlung mit throw für Job-Retry
- Metadata in OpenAI Calls für Tracking

## Zukunft Erweiterungen

Mögliche Verbesserungen:
- [ ] WebSocket/Pusher Benachrichtigung bei Fertigstellung
- [ ] Meal Replacement History/Undo Funktion
- [ ] Favoriten-System für beliebte Ersetzungen
- [ ] Batch-Replacement (mehrere Mahlzeiten auf einmal)
- [ ] User Feedback für KI-Training
- [ ] Caching ähnlicher Anfragen

## Monitoring

Wichtige Logs zu überwachen:
- `Starting meal replacement` - Job Start
- `Meal replaced successfully` - Erfolgreiche Ersetzung
- `Failed to replace meal` - Fehler

Metriken:
- OpenAI API Response Zeit
- Job Success/Failure Rate
- Durchschnittliche Verarbeitungszeit
- Nutzung mit vs. ohne Hinweis

