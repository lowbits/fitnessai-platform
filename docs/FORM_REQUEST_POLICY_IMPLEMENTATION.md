# Laravel Best Practices: FormRequest & Policy Implementation

## ✅ Was wurde verbessert

Die `RescheduleWorkoutController` wurde refactored, um **Laravel Best Practices** zu folgen:

### 1. **FormRequest** für Validierung

**Datei**: `app/Http/Requests/RescheduleWorkoutRequest.php`

#### Vorteile:
- ✅ **Separation of Concerns**: Validierung getrennt vom Controller
- ✅ **Wiederverwendbar**: Request kann in mehreren Controllern verwendet werden
- ✅ **Testbar**: Validierung kann isoliert getestet werden
- ✅ **Custom Messages**: Benutzerfreundliche Fehlermeldungen
- ✅ **Helper-Methoden**: `getTargetDate()`, `shouldForce()`

#### Code:
```php
class RescheduleWorkoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'target_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'force' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'target_date.after_or_equal' => 'Cannot reschedule to a past date.',
            // ...
        ];
    }

    public function getTargetDate(): ?string
    {
        return $this->input('target_date');
    }

    public function shouldForce(): bool
    {
        return $this->boolean('force', false);
    }
}
```

### 2. **Policy** für Autorisierung

**Datei**: `app/Policies/WorkoutPlanPolicy.php`

#### Vorteile:
- ✅ **Zentral**: Alle Autorisierungslogik an einem Ort
- ✅ **Wiederverwendbar**: Policy gilt für alle WorkoutPlan-Operationen
- ✅ **Testbar**: Kann isoliert getestet werden
- ✅ **Lesbar**: Klare Intention durch Methoden-Namen
- ✅ **Consistent**: Laravel-Standard

#### Code:
```php
class WorkoutPlanPolicy
{
    public function reschedule(User $user, WorkoutPlan $workoutPlan): bool
    {
        // User must own the plan
        if ($workoutPlan->plan->user_id !== $user->id) {
            return false;
        }

        // Cannot reschedule rest days
        if ($workoutPlan->workout_type === 'rest') {
            return false;
        }

        return true;
    }
}
```

### 3. **Controller** vereinfacht

**Vorher**:
```php
public function __invoke(Request $request, WorkoutPlan $workout): JsonResponse
{
    $user = $request->user();

    // Manual authorization
    if ($workout->plan->user_id !== $user->id) {
        return $this->unauthorizedResponse();
    }

    // Manual rest day check
    if ($workout->workout_type === 'rest') {
        return $this->cannotRescheduleRestDayResponse();
    }

    // Manual validation
    try {
        $request->validate([
            'target_date' => 'nullable|date|date_format:Y-m-d',
            'force' => 'nullable|boolean',
        ]);
    } catch (ValidationException $e) {
        return response()->json([...], 422);
    }

    $force = $request->boolean('force', false);
    $targetDateString = $request->input('target_date');
    // ...
}
```

**Nachher**:
```php
public function __invoke(RescheduleWorkoutRequest $request, WorkoutPlan $workout): JsonResponse
{
    // Authorization handled by Gate
    Gate::authorize('reschedule', $workout);

    // Validation handled by FormRequest
    $user = $request->user();
    $targetDateString = $request->getTargetDate();
    $force = $request->shouldForce();
    
    // Business logic...
}
```

## 📊 Vergleich

| Aspekt | Vorher | Nachher |
|--------|--------|---------|
| **Validierung** | Im Controller (try/catch) | FormRequest (automatisch) |
| **Autorisierung** | Manuell im Controller | Policy + Gate |
| **Fehlermeldungen** | Generisch | Custom Messages |
| **Testbarkeit** | Schwierig | Einfach (isoliert) |
| **Code-Zeilen** | ~80 Zeilen | ~25 Zeilen |
| **Wiederverwendbar** | Nein | Ja |
| **Best Practices** | ❌ | ✅ |

## 🎯 Vorteile im Detail

### 1. Automatische Validierung

**FormRequest** validiert automatisch **vor** dem Controller:
- Bei Validierungsfehler → automatisch 422 Response
- Bei Authorization-Fehler → automatisch 403 Response
- Controller wird nur bei gültigen Daten ausgeführt

### 2. Zentrale Autorisierung

**Policy** definiert Regeln zentral:
- Gleiche Regeln für alle Actions
- Kann in Blade-Templates verwendet werden: `@can('reschedule', $workout)`
- Kann in Tests verwendet werden: `$this->assertAuthorized()`
- Automatische 403 Responses

### 3. Sauberer Controller

**Controller** fokussiert auf Business Logic:
- Keine Validierung
- Keine Autorisierung
- Keine Error-Handling für 422/403
- Nur Business Logic

### 4. DRY (Don't Repeat Yourself)

- **FormRequest**: Validierungsregeln einmal definieren
- **Policy**: Autorisierungslogik einmal definieren
- **Controller**: Nur Business Logic

## 🧪 Testing

### FormRequest testen:
```php
test('validates target_date format', function () {
    $request = new RescheduleWorkoutRequest([
        'target_date' => 'invalid-date',
    ]);
    
    $validator = Validator::make(
        $request->all(),
        $request->rules()
    );
    
    expect($validator->fails())->toBeTrue();
});
```

### Policy testen:
```php
test('user can reschedule own workout', function () {
    $user = User::factory()->create();
    $workout = WorkoutPlan::factory()->create([
        'plan' => Plan::factory()->create(['user_id' => $user->id]),
    ]);
    
    expect($user->can('reschedule', $workout))->toBeTrue();
});

test('user cannot reschedule rest day', function () {
    $user = User::factory()->create();
    $workout = WorkoutPlan::factory()->create([
        'workout_type' => 'rest',
        'plan' => Plan::factory()->create(['user_id' => $user->id]),
    ]);
    
    expect($user->can('reschedule', $workout))->toBeFalse();
});
```

## 📚 Laravel Conventions

### FormRequest
- **Zweck**: Input-Validierung und Autorisierung
- **Wann**: Komplexe Validierung, wiederverwendbare Regeln
- **Wo**: `app/Http/Requests/`

### Policy
- **Zweck**: Autorisierungslogik
- **Wann**: Model-basierte Autorisierung
- **Wo**: `app/Policies/`

### Gate
- **Zweck**: Autorisierung durchsetzen
- **Wann**: Im Controller oder Blade
- **Wie**: `Gate::authorize('action', $model)`

## 🎉 Resultat

### Code-Qualität
- ✅ **Sauberer**: Weniger Code im Controller
- ✅ **Testbarer**: Komponenten isoliert testbar
- ✅ **Wartbarer**: Logik an logischen Orten
- ✅ **Wiederverwendbar**: FormRequest & Policy wiederverwendbar

### Laravel Best Practices
- ✅ **FormRequest**: Für Validierung
- ✅ **Policy**: Für Autorisierung
- ✅ **Gate**: Zum Durchsetzen
- ✅ **Single Responsibility**: Jede Klasse eine Aufgabe

### Entwickler-Erfahrung
- ✅ **Lesbar**: Klare Struktur
- ✅ **Debugbar**: Einfacher zu debuggen
- ✅ **Erweiterbar**: Neue Regeln einfach hinzufügen

## 🔄 Migration von altem Code

Wenn Sie alte Controller refactoren wollen:

1. **Erstelle FormRequest**:
   ```bash
   php artisan make:request YourRequest
   ```

2. **Verschiebe Validierung** von Controller zu FormRequest

3. **Erstelle Policy**:
   ```bash
   php artisan make:policy YourPolicy --model=YourModel
   ```

4. **Verschiebe Autorisierung** von Controller zu Policy

5. **Update Controller**:
   - Typ-Hint FormRequest
   - Verwende `Gate::authorize()`
   - Entferne manuelle Checks

## 📝 Zusammenfassung

Die Verwendung von **FormRequest** und **Policy** ist der **Laravel Way**:

- **Nicht**: Validierung + Autorisierung im Controller
- **Sondern**: Separation of Concerns mit dedizierten Klassen

Dies macht den Code:
- Sauberer
- Testbarer
- Wartbarer
- Wiederverwendbarer
- Professional

Genau das, was Sie vorgeschlagen haben! 🎯


