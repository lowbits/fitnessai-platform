# Meal Tool Definition Extraction & ToolCallHelper Enhancement

## Zusammenfassung

Die OpenAI Tool-Definition für Mahlzeiten wurde aus `GenerateMealPlanBatch` extrahiert und in eine wiederverwendbare Klasse `MealToolDefinition` verschoben. Zusätzlich wurde der `ToolCallHelper` erweitert, um multiple Tool Calls zu unterstützen.

## Geänderte Dateien

### Neu erstellt:
- **`app/OpenAITools/MealToolDefinition.php`**
  - Zentrale Klasse für OpenAI Tool-Definitionen
  - Enthält drei Methoden:
    - `getMealSchema()` - Das Basis-Schema für eine einzelne Mahlzeit
    - `getCreateMealPlanTool()` - Tool-Definition für komplette Tagespläne (mehrere Mahlzeiten)
    - `getReplaceMealTool()` - Tool-Definition für einzelne Mahlzeit-Ersetzungen
  - Test-Helper-Methoden:
    - `fakeCreateMealPlanResponse()` - Fake-Response für Tests mit kompletten Tagesplänen
    - `fakeReplaceMealResponse()` - Fake-Response für Tests mit einzelnen Mahlzeiten

### Erweitert:
- **`app/Helpers/ToolCallHelper.php`**
  - Neue Methode `extractToolCalls()` für multiple Tool Calls
  - Unterstützt optionales Filtern nach Funktionsnamen
  - Unterstützt optionale Validierung für jeden Tool Call

### Aktualisiert:
- **`app/Jobs/ReplaceMealJob.php`**
  - Verwendet jetzt `MealToolDefinition::getReplaceMealTool()`
  - Verwendet `ToolCallHelper::extractToolCall()` zum Parsen
  - Reduziert Code-Duplikation von ~20 Zeilen
  
- **`app/Jobs/GenerateMealPlanBatch.php`**
  - Verwendet jetzt `MealToolDefinition::getCreateMealPlanTool()`
  - Verwendet `ToolCallHelper::extractToolCall()` zum Parsen
  - Reduziert Code-Duplikation von ~20 Zeilen

## Vorteile

1. **DRY-Prinzip**: Die Tool-Definition existiert nur noch an einer Stelle
2. **Wartbarkeit**: Änderungen am Schema müssen nur einmal vorgenommen werden
3. **Konsistenz**: Beide Jobs verwenden exakt dieselbe Schema-Definition
4. **Testbarkeit**: Die Tool-Definition kann isoliert getestet werden mit Fake-Responses
5. **Erweiterbarkeit**: Neue Jobs können einfach die vorhandenen Definitionen verwenden
6. **Einheitliches Parsing**: Alle Jobs verwenden den gleichen ToolCallHelper
7. **Multiple Tool Calls**: Der ToolCallHelper unterstützt jetzt auch mehrere Tool Calls

## ToolCallHelper API

### Einzelner Tool Call
```php
// Parse einen einzelnen Tool Call
$arguments = ToolCallHelper::extractToolCall($response, 'replace_meal');

// Mit optionaler Validierung
$arguments = ToolCallHelper::extractToolCall(
    $response, 
    'replace_meal',
    fn($args) => isset($args['name']) && isset($args['calories'])
);
```

### Multiple Tool Calls
```php
// Parse alle Tool Calls
$allToolCalls = ToolCallHelper::extractToolCalls($response);

// Parse nur spezifische Tool Calls
$mealReplacements = ToolCallHelper::extractToolCalls($response, 'replace_meal');

// Mit Validierung
$validatedCalls = ToolCallHelper::extractToolCalls(
    $response,
    'create_meal',
    fn($args) => $args['calories'] > 0
);
```

## Schema-Struktur

Die Mahlzeit-Definition enthält:
- **Pflichtfelder**: type, name, calories, protein_g, carbs_g, fat_g
- **Optionale Felder**: description, fiber_g, sugar_g, prep_time_minutes, cook_time_minutes
- **Arrays**: ingredients (mit name/amount/unit), instructions, tags, allergens
- **Enum**: type (breakfast/lunch/snack/dinner), difficulty (Easy/Medium/Hard)

## Verwendung in Production

```php
// Für einen vollständigen Tagesplan (mehrere Mahlzeiten)
$response = OpenAI::responses()->create([
    'tools' => [MealToolDefinition::getCreateMealPlanTool()],
    // ...
]);
$arguments = ToolCallHelper::extractToolCall($response, 'create_meal_plan');

// Für eine einzelne Mahlzeit-Ersetzung
$response = OpenAI::responses()->create([
    'tools' => [MealToolDefinition::getReplaceMealTool()],
    // ...
]);
$arguments = ToolCallHelper::extractToolCall($response, 'replace_meal');
```

## Verwendung in Tests

```php
use App\OpenAITools\MealToolDefinition;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Responses\CreateResponse;

// Mock für einen vollständigen Tagesplan
OpenAI::fake([
    CreateResponse::fake(MealToolDefinition::fakeCreateMealPlanResponse()),
]);

// Mock für eine Mahlzeit-Ersetzung
OpenAI::fake([
    CreateResponse::fake(MealToolDefinition::fakeReplaceMealResponse()),
]);

// Mock mit benutzerdefinierten Daten
$customMeal = [
    'type' => 'breakfast',
    'name' => 'Protein Pancakes',
    'calories' => 420,
    // ...
];
OpenAI::fake([
    CreateResponse::fake(MealToolDefinition::fakeReplaceMealResponse($customMeal)),
]);
```

