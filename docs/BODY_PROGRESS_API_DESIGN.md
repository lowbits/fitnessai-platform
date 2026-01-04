# Body Progress Tracking - API Design ✅

## Implementierung abgeschlossen

### 🎯 Design-Prinzipien

**Saubere API mit interner Konsistenz**

```
┌─────────────────┐         ┌──────────────────┐         ┌─────────────────┐
│   Mobile App    │  ─────> │   Laravel API    │  ─────> │    Database     │
│                 │         │                  │         │                 │
│  weight: 75.5   │         │  Accessor/Map    │         │  weight_kg      │
│  muscle_mass    │         │  Clean → _kg     │         │  muscle_mass_kg │
│  waist_circ     │         │  Names   _cm     │         │  waist_circ_cm  │
└─────────────────┘         └──────────────────┘         └─────────────────┘
   Clean API Names          Laravel Accessors           DB with Units
```

### ✅ Vorteile

1. **Entwicklerfreundlich**: Mobile-Entwickler arbeiten mit `weight`, nicht `weight_kg`
2. **Zukunftssicher**: Bereit für imperial/metric ohne API-Breaking-Changes
3. **Klare Datenbank**: Spalten zeigen Einheiten (`_kg`, `_cm`)
4. **Single Source of Truth**: Alle Werte in kg/cm gespeichert

### 📝 API Beispiele

**Request (Clean):**
```json
{
  "weight": 75.5,
  "body_fat_percentage": 18.5,
  "muscle_mass": 65.0,
  "waist_circumference": 85.0
}
```

**Response (Clean):**
```json
{
  "data": {
    "id": 1,
    "weight": "75.50",
    "body_fat_percentage": "18.50",
    "muscle_mass": "65.00",
    "waist_circumference": "85.00"
  }
}
```

**Database (With Units):**
```sql
weight_kg: 75.50
body_fat_percentage: 18.50
muscle_mass_kg: 65.00
waist_circumference_cm: 85.00
```

### 🔧 Implementierung

**Model: BodyProgress.php**
```php
// Hidden: Database fields with suffixes
protected $hidden = [
    'weight_kg',
    'muscle_mass_kg',
    'waist_circumference_cm',
    // ...
];

// Appended: Clean API fields
protected $appends = [
    'weight',
    'muscle_mass',
    'waist_circumference',
    // ...
];

// Accessor: Map clean name to DB field
public function getWeightAttribute(): ?float {
    return $this->weight_kg;
}
```

**Controller: BodyProgressController.php**
```php
// Map API input → DB fields
$bodyProgress = $user->bodyProgress()->create([
    'weight_kg' => $request->weight,           // API: weight
    'muscle_mass_kg' => $request->muscle_mass, // API: muscle_mass
    'waist_circumference_cm' => $request->waist_circumference,
    // ...
]);

// Response uses accessors automatically
return response()->json([
    'data' => $bodyProgress // Returns weight, not weight_kg
]);
```

### 🌍 Zukünftige Unterstützung: Imperial/Metric

**Phase 1 (Aktuell): Nur Metric**
```php
public function getWeightAttribute(): ?float {
    return $this->weight_kg;
}
```

**Phase 2 (Zukünftig): User Preference**
```php
public function getWeightAttribute(): ?float {
    $user = $this->user;
    
    if ($user->measurement === 'imperial') {
        return round($this->weight_kg * 2.20462, 2); // kg → lbs
    }
    
    return $this->weight_kg; // kg
}
```

**Keine API-Änderungen nötig!** 🎉

### 📊 Vergleich mit anderen Frameworks

**Option 1: Suffixe in API (Abgelehnt)**
```javascript
// ❌ Schlecht: Entwickler müssen sich Einheiten merken
{ weight_kg: 75.5, height_cm: 180 }
```

**Option 2: Saubere API (Implementiert)** ✅
```javascript
// ✅ Gut: Intuitive API
{ weight: 75.5, height: 180 }
```

**Option 3: Units als separates Feld (Overkill)**
```javascript
// ❌ Zu komplex für unseren Use Case
{ 
  weight: { value: 75.5, unit: "kg" },
  height: { value: 180, unit: "cm" }
}
```

### 🎯 Weitere Best Practices

**1. Konsistenz bei Accessors**
- Alle Circumference-Felder folgen demselben Pattern
- Muscle Mass mapped wie Weight
- Body Fat Percentage hat kein Mapping (keine Einheit)

**2. Validation bleibt sauber**
```php
'weight' => 'required|numeric|min:20|max:500'
// Nicht: 'weight_kg' => ...
```

**3. Frontend Code ist intuitiv**
```javascript
const trackWeight = async (weight) => {
  await api.post('/track/body-progress', { weight });
};
```

### 🧪 Tests

Alle 15 Tests verwenden die saubere API:
```php
test('user can track body progress with only weight', function () {
    $response = $this->postJson('/api/v2/track/body-progress', [
        'weight' => 75.50, // Clean API name
    ]);
    
    $response->assertJson([
        'data' => ['weight' => '75.50'] // Clean response
    ]);
    
    $this->assertDatabaseHas('body_progress', [
        'weight_kg' => 75.50 // DB with suffix
    ]);
});
```

### 📚 Dokumentation

- ✅ `docs/BODY_PROGRESS_TRACKING.md` - Vollständige API-Doku mit clean names
- ✅ `docs/BODY_PROGRESS_IMPLEMENTATION_SUMMARY.md` - Implementierungsdetails
- ✅ Alle Code-Beispiele verwenden saubere API-Namen

### 🚀 Deployment Checklist

- [x] Migration erstellt (weight_kg, etc.)
- [x] Model mit Accessors und Hidden Fields
- [x] Controller mit Input-Mapping
- [x] Tests mit clean API names
- [x] Dokumentation aktualisiert
- [x] Factory für Tests
- [x] User.getCurrentWeight() Methode
- [x] AuthController verwendet getCurrentWeight()

### ✨ Ergebnis

**API für Entwickler: Einfach und sauber**
```javascript
{ weight: 75.5, muscle_mass: 65.0 }
```

**Datenbank für System: Klar und präzise**
```sql
weight_kg, muscle_mass_kg
```

**Bestes aus beiden Welten!** 🎉

---

**Status**: ✅ Production Ready  
**API Version**: v2  
**Erstellt**: 2026-01-04  
**Breaking Changes**: Keine (neue API)

