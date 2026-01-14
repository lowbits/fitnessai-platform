# Meal Plan Generation: Responses API Refactoring

## Übersicht

Das `GenerateUserMealPlan` Job wurde von der `chat()` API zu der neueren `responses()` API refactored. Dies ermöglicht besseres Context-Chaining zwischen den Tagen und effizienteres Prompt-Caching.

## Datum

13. Januar 2026

## Änderungen

### 1. Migration: `openai_response_id` Feld

**Datei**: `database/migrations/2026_01_13_161515_add_openai_response_id_to_meal_plans_table.php`

```php
$table->string('openai_response_id')->nullable()->after('status');
```

Dieses Feld speichert die OpenAI Response ID für jede generierte Mahlzeit, die dann für Context-Chaining verwendet wird.

### 2. Model Update: MealPlan

**Datei**: `app/Models/MealPlan.php`

- `openai_response_id` zum `$fillable` Array hinzugefügt

### 3. Job Refactoring: GenerateUserMealPlan

**Datei**: `app/Jobs/GenerateUserMealPlan.php`

#### Vollständiges Beispiel der Responses API Anfrage:

```php
$response = $client->responses()->create([
    'model' => 'gpt-5-mini',
    'instructions' => $instructions, // System prompt
    'previous_response_id' => $previousResponseId, // Context chaining
    'input' => "Generate a complete day meal plan for day {$day}. Include breakfast, lunch, snack, and dinner.",
    'tools' => [
        [
            'type' => 'function',
            'name' => 'create_meal_plan', // WICHTIG: name direkt hier!
            'description' => 'Creates a complete daily meal plan with all meals',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'meals' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'type' => ['type' => 'string', 'enum' => ['breakfast', 'lunch', 'snack', 'dinner']],
                                'name' => ['type' => 'string'],
                                'calories' => ['type' => 'integer'],
                                'protein_g' => ['type' => 'integer'],
                                'carbs_g' => ['type' => 'integer'],
                                'fat_g' => ['type' => 'integer'],
                                // ... weitere properties
                            ],
                            'required' => ['type', 'name', 'calories', 'protein_g', 'carbs_g', 'fat_g'],
                        ],
                    ],
                ],
                'required' => ['meals'],
            ],
        ],
    ],
    'tool_choice' => 'required', // Nur 'none', 'auto' oder 'required'!
    'store' => true,
    'metadata' => [
        'user_id' => (string) $this->user->id,
        'plan_id' => (string) $this->plan->id,
        'day_number' => (string) $day,
    ],
]);

// Response parsing - WICHTIG: output sind Objekte, nicht Arrays!
// Output enthält mehrere Items: reasoning + function_call
$toolCall = null;
foreach ($response->output ?? [] as $item) {
    if ($item->type === 'function_call' && $item->name === 'create_meal_plan') {
        $toolCall = $item;
        break;
    }
}

if (!$toolCall) {
    throw new \RuntimeException('Function call missing in Responses output');
}

$arguments = json_decode($toolCall->arguments, true);
```

**Kritische Unterschiede zur Chat API:**

### 1. Input Format

**Chat API:**
```php
'messages' => [
    ['role' => 'system', 'content' => $systemPrompt],
    ['role' => 'user', 'content' => $userMessage],
]
```

**Responses API:**
```php
'instructions' => $systemPrompt,  // System message
'input' => $userMessage,          // User message als String, NICHT Array!
```

### 2. Tool-Definition

**Chat API:**
```php
'tools' => [
    [
        'type' => 'function',
        'function' => [
            'name' => 'create_meal_plan',
            'description' => '...',
            'parameters' => [...]
        ]
    ]
]
```

**Responses API:**
```php
'tools' => [
    [
        'type' => 'function',
        'name' => 'create_meal_plan',      // DIREKT hier, nicht in 'function'
        'description' => '...',             // DIREKT hier
        'parameters' => [...]               // DIREKT hier
    ]
]
```

### 3. Tool Choice

**Chat API:**
```php
'tool_choice' => [
    'type' => 'function',
    'function' => ['name' => 'create_meal_plan']
]
```

**Responses API:**
```php
'tool_choice' => 'required'  // Nur 'none', 'auto' oder 'required'!
```

**Wichtig:** Responses API unterstützt NICHT das Erzwingen einer bestimmten Function. Nur generelle Werte:
- `'none'`: Keine Tool-Nutzung
- `'auto'`: Model entscheidet
- `'required'`: Tool MUSS genutzt werden (aber welches, entscheidet das Model)

### 4. Response-Struktur

**Chat API:**
```php
$toolCall = $response->choices[0]->message->toolCalls[0] ?? null;
```

**Responses API:**
```php
// Response->output is an array of items with different types
// Example: [reasoning item, function_call item]
$toolCall = null;
foreach ($response->output ?? [] as $item) {
    if ($item->type === 'function_call' && $item->name === 'create_meal_plan') {
        $toolCall = $item;
        break;
    }
}
$arguments = json_decode($toolCall->arguments, true);
```

**Wichtig:** 
- `output` ist ein Array von Objekten, nicht ein verschachteltes `choices` Array
- Jedes `output` Item hat einen `type` (z.B. `'reasoning'` oder `'function_call'`)
- Function calls haben `name`, `arguments`, `callId`, `status` properties
- `arguments` ist ein JSON String, der geparsed werden muss

## Hauptänderungen im Code

### a) Von `chat()` zu `responses()` API

Die komplette API-Struktur wurde angepasst.

### b) `instructions` statt `system` Message

- System Prompt wird jetzt als `instructions` Parameter übergeben
- Bei Verwendung von `previous_response_id` werden Instructions NICHT vom vorherigen Response übernommen
- Dies macht es einfach, System-Messages zwischen Responses auszutauschen

### c) Context-Chaining mit `previous_response_id`

Neue Methode hinzugefügt:

```php
private function getPreviousResponseId(int $currentDay): ?string
{
    if ($currentDay <= 1) {
        return null;
    }

    $previousMealPlan = MealPlan::where('plan_id', $this->plan->id)
        ->where('day_number', $currentDay - 1)
        ->where('status', 'generated')
        ->first();

    return $previousMealPlan?->openai_response_id;
}
```

Für Tag 2+ wird automatisch die Response ID vom vorherigen Tag verwendet, wodurch das Modell Kontext über bereits generierte Mahlzeiten hat.

### d) `store: true` für Prompt Caching

- Aktiviert automatisches Caching von Instructions und anderen Prompt-Teilen
- Reduziert Token-Kosten bei wiederholten Anfragen mit gleichen Instructions
- Besonders effektiv bei der 7-Tage-Batch-Generierung

### e) Entfernung von `generatedMealsSummary`

**Vorher:**
```php
// Track generated meals for context (lightweight summary)
$generatedMealsSummary = [];

// ... später
foreach ($arguments['meals'] as $meal) {
    $generatedMealsSummary[] = "{$meal['type']}: {$meal['name']}";
}
```

**Nachher:**
Nicht mehr nötig - der `previous_response_id` übernimmt automatisch den kompletten Kontext.

### f) Response ID Speicherung

```php
$mealPlan->update([
    'status' => 'generated',
    'total_calories' => $totals['calories'],
    'total_protein_g' => $totals['protein_g'],
    'total_carbs_g' => $totals['carbs_g'],
    'total_fat_g' => $totals['fat_g'],
    'openai_response_id' => $response->id, // Neu: Store für Context-Chaining
]);
```

### g) Metadata für Tracking

```php
'metadata' => [
    'user_id' => (string) $this->user->id,
    'plan_id' => (string) $this->plan->id,
    'day_number' => (string) $day,
]
```

Ermöglicht besseres Tracking und Debugging in OpenAI Dashboard.

## Vorteile

### 1. **Besserer Kontext**
- Das Modell hat automatisch Zugriff auf alle vorherigen Responses (nicht nur eine Summary)
- Bessere Vermeidung von wiederholten Mahlzeiten
- Kohärentere Meal Plans über die Woche

### 2. **Einfacherer Code**
- Keine manuelle `generatedMealsSummary` Verwaltung mehr
- Weniger Code für Context-Handling
- Automatisches Context-Management durch OpenAI

### 3. **Kosteneinsparungen**
- `store: true` aktiviert Prompt-Caching
- Instructions müssen nicht bei jedem Request neu verarbeitet werden
- Reduzierte Token-Kosten besonders bei langen System-Prompts

### 4. **Flexiblere Instructions**
- Instructions werden nicht automatisch vom vorherigen Response übernommen
- Einfaches Anpassen von System-Messages zwischen Requests
- Mehr Kontrolle über den Kontext

### 5. **Verbesserte Nachvollziehbarkeit**
- `openai_response_id` wird in der Datenbank gespeichert
- Debugging und Nachvollziehbarkeit der Generierung verbessert
- Direkte Verknüpfung zu OpenAI Responses möglich
- Metadata für besseres Tracking

## Technische Details

### Responses API vs Chat API - Vergleichstabelle

| Feature | Chat API | Responses API |
|---------|----------|---------------|
| System Message | `messages[role=system]` | `instructions` Parameter |
| User Input | `messages[role=user]` (Array) | `input` (String) |
| Context | Manuell via Messages | `previous_response_id` |
| Caching | Nicht verfügbar | `store: true` |
| Response Chain | Manuell | Automatisch |
| Response Structure | `choices[0]->message` | `output[0]` |
| Tool Definition | `tools[0].function.name` | `tools[0].name` |
| Tool Choice | Object mit type+function | `'none'`, `'auto'` oder `'required'` |
| Metadata Support | Nein | `metadata` Parameter |

### Context-Chaining Flow

```
Tag 1: Generate Meal Plan
  ↓
  response_id: resp_abc123
  ↓
  Speichern in DB

Tag 2: Generate Meal Plan
  ↓
  previous_response_id: resp_abc123
  ↓
  Model hat Kontext von Tag 1
  ↓
  response_id: resp_def456
  ↓
  Speichern in DB

Tag 3: Generate Meal Plan
  ↓
  previous_response_id: resp_def456
  ↓
  Model hat Kontext von Tag 1+2
  ↓
  ... und so weiter
```

## Migration ausführen

```bash
php artisan migrate
```

## Testing

Nach dem Deployment sollte getestet werden:

1. **Neue Meal Plan Generation**
   - Erstelle einen neuen Plan
   - Prüfe, ob `openai_response_id` gespeichert wird
   - Prüfe, ob Context-Chaining funktioniert (keine wiederholten Mahlzeiten)

2. **Logging überprüfen**
   - `previous_response_id` sollte in Logs erscheinen
   - `openai_response_id` sollte bei erfolgreicher Generierung geloggt werden

3. **Performance/Kosten**
   - Token-Usage mit `store: true` sollte reduziert sein
   - Prompt Tokens sollten nach dem ersten Request niedriger sein (Caching)

## Häufige Fehler

### Fehler: "Missing required parameter: 'tools[0].name'"

**Problem:** Tool-Struktur ist falsch - `name` muss direkt im Tool-Array sein, nicht in `function`.

**Falsch:**
```php
'tools' => [
    [
        'type' => 'function',
        'function' => [
            'name' => 'create_meal_plan',
            // ...
        ]
    ]
]
```

**Richtig:**
```php
'tools' => [
    [
        'type' => 'function',
        'name' => 'create_meal_plan',
        'description' => '...',
        'parameters' => [...]
    ]
]
```

### Fehler: "Unsupported parameter: 'messages'"

**Problem:** Responses API verwendet `input` statt `messages`.

**Falsch:**
```php
'messages' => [
    ['role' => 'user', 'content' => $message]
]
```

**Richtig:**
```php
'input' => $message
```

### Fehler: "Invalid value: 'create_meal_plan'. Supported values are: 'none', 'auto', and 'required'."

**Problem:** Responses API unterstützt nicht das Erzwingen einer bestimmten Function.

**Falsch:**
```php
'tool_choice' => 'create_meal_plan'
// oder
'tool_choice' => ['type' => 'function', 'function' => ['name' => 'create_meal_plan']]
```

**Richtig:**
```php
'tool_choice' => 'required'  // Zwingt zur Tool-Nutzung (Model wählt welches)
// oder
'tool_choice' => 'auto'      // Model entscheidet
// oder
'tool_choice' => 'none'      // Keine Tools
```

**Workaround:** Wenn du nur ein Tool definierst und `'required'` verwendest, wird automatisch dieses Tool genutzt.

### Fehler: "No tool call received"

**Problem:** Response-Parsing ist falsch - `output` enthält verschiedene Item-Typen.

**Response Struktur:**
```json
{
  "id": "resp_...",
  "output": [
    {
      "type": "reasoning",
      "id": "rs_...",
      "summary": []
    },
    {
      "type": "function_call",
      "name": "create_meal_plan",
      "arguments": "{...}",
      "callId": "call_...",
      "status": "completed"
    }
  ]
}
```

**Falsch:**
```php
$toolCall = $response->output[0]->toolCalls[0];
```

**Richtig:**
```php
foreach ($response->output ?? [] as $item) {
    if ($item->type === 'function_call' && $item->name === 'create_meal_plan') {
        $toolCall = $item;
        break;
    }
}
$arguments = json_decode($toolCall->arguments, true);
```

## Rückwärts-Kompatibilität

- Bestehende Meal Plans ohne `openai_response_id` funktionieren weiterhin
- Feld ist `nullable`, daher keine Breaking Changes
- Neue Generierungen nutzen automatisch das neue System

## Weiterführende Dokumentation

- OpenAI Responses API: https://platform.openai.com/docs/api-reference/responses
- Prompt Caching: https://platform.openai.com/docs/guides/prompt-caching

