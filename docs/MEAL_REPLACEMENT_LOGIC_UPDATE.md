# Meal Replacement Logic Update

## Zusammenfassung

Die Logik für das Ersetzen von Mahlzeiten wurde umstrukturiert, um besseres Tracking und UI-Feedback zu ermöglichen. **Jede Mahlzeit hat jetzt ihren eigenen Status** für granulares UI-Feedback.

## Änderungen

### Konzept
Anstatt eine Mahlzeit zu aktualisieren, wird die alte Mahlzeit **soft deleted** und eine **neue Mahlzeit erstellt**. Der Status wird auf **beiden Ebenen** verwaltet:
- **meal_plan.status** - Gesamtstatus des Plans
- **meal.status** - Individueller Status jeder Mahlzeit

### Vorteile

1. **Granulares UI-Feedback**
   - Jede Mahlzeit hat ihren eigenen Status
   - Während Replacement: 3 Mahlzeiten "generated", 1 Mahlzeit "generating"
   - Frontend kann gezielt die generierende Mahlzeit anzeigen

2. **Tracking nicht gemochter Mahlzeiten**
   - Gelöschte Mahlzeiten bleiben in der Datenbank (soft delete)
   - Ermöglicht Analyse, welche Mahlzeiten Nutzer ersetzen
   - Kann für zukünftige Verbesserungen verwendet werden

3. **Bessere Fehlerbehandlung**
   - Bei Fehler wird `meal.status = 'failed'` gesetzt
   - Fehlerhafte Mahlzeit bleibt sichtbar für User
   - Alte Mahlzeit wird wiederhergestellt

4. **Audit Trail**
   - Historie aller Mahlzeitenänderungen verfügbar
   - Soft deleted Mahlzeiten können analysiert werden

## Datenbank-Änderungen

### Migration 1: `add_soft_deletes_to_meals_table`
```php
Schema::table('meals', function (Blueprint $table) {
    $table->softDeletes()->after('updated_at');
    $table->index('deleted_at');
});
```

### Migration 2: `add_status_to_meals_table`
```php
Schema::table('meals', function (Blueprint $table) {
    $table->enum('status', ['pending', 'generating', 'generated', 'failed'])
        ->default('generated')
        ->after('allergens');
    $table->index('status');
});
```

### Meal Model
- Verwendet jetzt `SoftDeletes` Trait
- Hat `status` Feld mit Werten: `pending`, `generating`, `generated`, `failed`

## Job-Ablauf: ReplaceMealJob

### 1. Vorbereitung
```php
// Setze meal_plan status auf "generating"
$mealPlan->update(['status' => 'generating']);

// Erstelle Placeholder-Mahlzeit mit status "generating"
$newMeal = Meal::create([
    'type' => $this->meal->type,
    'name' => 'Generating...',
    'description' => 'Generating meal replacement...',
    'status' => 'generating',  // ✅ Individueller Status
    // ... mit gleichen Makros wie alte Mahlzeit
]);
```

### 2. API-Anfrage
```php
$response = OpenAI::responses()->create([
    'tools' => [MealToolDefinition::getReplaceMealTool()],
    'metadata' => [
        'old_meal_id' => (string) $this->meal->id,
        'new_meal_id' => (string) $newMeal->id,
    ],
]);
```

### 3. Erfolgreicher Abschluss
```php
// Aktualisiere neue Mahlzeit mit generierten Daten
$newMeal->update([
    ...$arguments,
    'status' => 'generated',  // ✅ Status auf "generated"
]);

// Soft delete alte Mahlzeit
$this->meal->delete(); // deleted_at wird gesetzt

// Aktualisiere meal_plan Summen und Status
$mealPlan->update([
    'status' => 'generated',
    'total_calories' => ...,
    // ...
]);
```

### 4. Fehlerfall
```php
// Setze meal status auf "failed"
$newMeal->update(['status' => 'failed']);  // ✅ Mahlzeit bleibt mit Fehler-Status

// Setze meal_plan status zurück auf "generated"
$mealPlan->update(['status' => 'generated']);

// Stelle alte Mahlzeit wieder her
$this->meal->restore();
```

## API-Verhalten für Frontend

### Während der Generierung
```json
{
  "meal_plan": {
    "id": 123,
    "status": "generating",
    "meals": [
      {
        "id": 1,
        "status": "generated",  // ✅ Diese Mahlzeit ist fertig
        "name": "Greek Yogurt Parfait"
      },
      {
        "id": 5,
        "status": "generating",  // ✅ Diese Mahlzeit wird generiert
        "name": "Generating..."
      },
      {
        "id": 3,
        "status": "generated",  // ✅ Diese Mahlzeit ist fertig
        "name": "Protein Smoothie"
      },
      {
        "id": 4,
        "status": "generated",  // ✅ Diese Mahlzeit ist fertig
        "name": "Grilled Salmon"
      }
    ]
  }
}
```

### Nach erfolgreicher Generierung
```json
{
  "meal_plan": {
    "id": 123,
    "status": "generated",
    "meals": [
      {
        "id": 1,
        "status": "generated",
        "name": "Greek Yogurt Parfait"
      },
      {
        "id": 6,  // ✅ Neue ID!
        "status": "generated",  // ✅ Jetzt "generated"
        "name": "Grilled Chicken Caesar Salad"
      },
      {
        "id": 3,
        "status": "generated",
        "name": "Protein Smoothie"
      },
      {
        "id": 4,
        "status": "generated",
        "name": "Grilled Salmon"
      }
    ]
  }
}
```

### Bei Fehler
```json
{
  "meal_plan": {
    "id": 123,
    "status": "generated",  // ✅ Plan-Status bleibt "generated"
    "meals": [
      {
        "id": 1,
        "status": "generated",
        "name": "Greek Yogurt Parfait"
      },
      {
        "id": 5,
        "status": "failed",  // ✅ Einzelne Mahlzeit zeigt Fehler
        "name": "Generating..."
      },
      {
        "id": 3,
        "status": "generated",
        "name": "Protein Smoothie"
      },
      {
        "id": 4,
        "status": "generated",
        "name": "Grilled Salmon"
      }
    ]
  }
}
```

## Frontend-Integration

### Empfohlenes Polling
```javascript
// Starte Mahlzeit-Ersetzung
await replaceMeal(mealId, hint);

// Polle meal_plan Status
const interval = setInterval(async () => {
  const mealPlan = await fetchMealPlan(mealPlanId);
  
  if (mealPlan.status === 'generated') {
    clearInterval(interval);
    // Zeige neue Mahlzeit
  } else if (mealPlan.status === 'failed') {
    clearInterval(interval);
    // Zeige Fehler
  }
}, 2000); // Alle 2 Sekunden
```

### UI-Status-Anzeige
```javascript
switch (mealPlan.status) {
  case 'generating':
    return <LoadingSpinner text="Generating meal..." />;
  case 'generated':
    return <MealCard meal={meal} />;
  case 'failed':
    return <ErrorMessage text="Failed to generate meal" />;
  case 'pending':
    return <EmptyState />;
}
```

## Abfragen mit Soft Deletes

### Nur aktive Mahlzeiten (Standard)
```php
$meals = $mealPlan->meals; // Soft deleted automatisch ausgeschlossen
```

### Inkl. gelöschter Mahlzeiten (für Analyse)
```php
$allMeals = $mealPlan->meals()->withTrashed()->get();
```

### Nur gelöschte Mahlzeiten (nicht gemochte)
```php
$deletedMeals = $mealPlan->meals()->onlyTrashed()->get();
```

## Testing

Tests wurden aktualisiert, um:
- Soft delete zu verifizieren (`expect($meal->trashed())->toBeTrue()`)
- Neue Mahlzeit-ID zu prüfen
- `meal_plan.status` zu validieren (nicht `meal.status`)

## Migration Hinweise

- ✅ Keine Daten-Migration nötig (nur Schema-Änderung)
- ✅ Abwärtskompatibel (soft deletes sind optional)
- ✅ Bestehende Mahlzeiten funktionieren weiter

