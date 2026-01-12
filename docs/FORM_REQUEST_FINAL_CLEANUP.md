# FormRequest & Policy - Final Implementation

## ✅ Was wurde korrigiert

### 1. **Unnötige Custom Messages entfernt**

**Problem**: Custom Messages für Standard-Laravel-Regeln sind überflüssig

**Vorher**:
```php
public function messages(): array
{
    return [
        'target_date.date' => 'The target date must be a valid date.',
        'target_date.date_format' => 'The target date must be in Y-m-d format (e.g., 2026-01-15).',
        'target_date.after_or_equal' => 'Cannot reschedule to a past date.',
        'force.boolean' => 'The force parameter must be true or false.',
    ];
}
```

**Nachher**:
```php
// Keine messages() Methode - Laravel default messages sind gut genug
```

**Laravel Default Messages**:
- `date`: "The :attribute must be a valid date."
- `date_format`: "The :attribute must match the format :format."
- `after_or_equal`: "The :attribute must be a date after or equal to :date."
- `boolean`: "The :attribute field must be true or false."

### 2. **Policy in FormRequest::authorize()**

**Problem**: Gate-Aufruf im Controller ist redundant

**Vorher (Controller)**:
```php
public function __invoke(Request $request, WorkoutPlan $workout): JsonResponse
{
    Gate::authorize('reschedule', $workout);
    
    // Manual validation
    $request->validate([...]);
    
    // Business logic
}
```

**Nachher (FormRequest)**:
```php
public function authorize(): bool
{
    return $this->user()->can('reschedule', $this->route('workout'));
}
```

**Nachher (Controller)**:
```php
public function __invoke(RescheduleWorkoutRequest $request, WorkoutPlan $workout): JsonResponse
{
    // Authorization & Validation already done by FormRequest!
    $user = $request->user();
    $targetDateString = $request->getTargetDate();
    $force = $request->shouldForce();
    
    // Business logic only
}
```

### 3. **Doppelte Validierung entfernt**

**Problem**: Validierung passierte zweimal

**Vorher**:
```php
// In FormRequest
public function rules(): array { return [...]; }

// Dann nochmal im Controller
$request->validate([
    'target_date' => 'nullable|date|date_format:Y-m-d',
    'force' => 'nullable|boolean',
]);
```

**Nachher**:
```php
// Nur einmal in FormRequest
public function rules(): array { return [...]; }

// Controller nutzt direkt die Helper-Methoden
$targetDateString = $request->getTargetDate();
$force = $request->shouldForce();
```

## 📊 Vorher vs. Nachher

### Controller Code

| Vorher | Nachher |
|--------|---------|
| ~45 Zeilen (Auth + Validation + Logic) | ~5 Zeilen (nur Logic) |
| Manual authorization checks | FormRequest::authorize() |
| Manual validation | FormRequest::rules() |
| try/catch blocks | Automatisch |
| Error responses | Automatisch |

### FormRequest

| Vorher | Nachher |
|--------|---------|
| authorize() immer true | authorize() nutzt Policy |
| Custom messages für Standard-Regeln | Keine custom messages |
| 65 Zeilen | 48 Zeilen |

## 🎯 Vorteile

### 1. **Single Source of Truth**

**Authorization**: Nur in Policy
```php
class WorkoutPlanPolicy {
    public function reschedule(User $user, WorkoutPlan $workout): bool
    {
        return $workout->plan->user_id === $user->id 
            && $workout->workout_type !== 'rest';
    }
}
```

**Validation**: Nur in FormRequest
```php
class RescheduleWorkoutRequest extends FormRequest {
    public function rules(): array
    {
        return [
            'target_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'force' => ['nullable', 'boolean'],
        ];
    }
}
```

### 2. **Automatische Error Responses**

**FormRequest** gibt automatisch zurück:
- **403 Forbidden**: Wenn `authorize()` false zurückgibt
- **422 Unprocessable Entity**: Wenn Validation fehlschlägt

**Keine** manuellen Response-Builder nötig!

### 3. **Laravel Default Messages sind gut**

Laravel's eingebaute Messages sind:
- ✅ **Professionell**
- ✅ **Übersetzt** (in 70+ Sprachen)
- ✅ **Konsistent** mit dem Rest der App
- ✅ **Wartbar** (Updates mit Laravel)

Nur custom messages verwenden wenn:
- ❌ Standard-Message unklar ist
- ❌ Business-spezifische Begriffe nötig
- ❌ Spezielle Formatierung gewünscht

### 4. **Richtiger HTTP Status Code**

**Rest Day Reschedule**:
- **Vorher**: 400 Bad Request (impliziert falscher Input)
- **Nachher**: 403 Forbidden (korrekt: nicht erlaubt)

**403 ist semantisch korrekt**:
- User ist authentifiziert ✅
- User hat Berechtigung für Workout ✅
- Aber diese **spezifische Aktion** ist nicht erlaubt ❌

## 🧪 Tests anpassen

```php
// Vorher
$response->assertStatus(400)
    ->assertJson([
        'error' => 'Invalid operation',
        'message' => 'Cannot reschedule a rest day',
    ]);

// Nachher
$response->assertStatus(403); // Policy authorization failed
```

## 📝 Best Practices

### ✅ DO:

1. **Policy in FormRequest::authorize()** verwenden
2. **Laravel Default Messages** nutzen
3. **Helper-Methoden** im Request für Business Logic
4. **Single Responsibility**: Request validiert, Policy autorisiert, Controller implementiert

### ❌ DON'T:

1. **Keine doppelte Validierung** (Request + Controller)
2. **Keine custom messages** für Standard-Regeln
3. **Kein Gate im Controller** wenn FormRequest existiert
4. **Keine manuellen Auth-Checks** im Controller

## 🎉 Resultat

**FormRequest**:
```php
class RescheduleWorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reschedule', $this->route('workout'));
    }

    public function rules(): array
    {
        return [
            'target_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'force' => ['nullable', 'boolean'],
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

**Controller**:
```php
public function __invoke(RescheduleWorkoutRequest $request, WorkoutPlan $workout): JsonResponse
{
    // Clean! Nur Business Logic!
    $user = $request->user();
    $targetDate = $request->getTargetDate();
    $force = $request->shouldForce();
    
    return DB::transaction(function () use ($user, $workout, $targetDate, $force) {
        // Business logic...
    });
}
```

**Policy**:
```php
public function reschedule(User $user, WorkoutPlan $workoutPlan): bool
{
    return $workoutPlan->plan->user_id === $user->id
        && $workoutPlan->workout_type !== 'rest';
}
```

**Clean, Simple, Laravel Way!** ✨


