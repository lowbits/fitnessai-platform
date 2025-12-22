# Email Verification Workflow

## Übersicht

Das Onboarding-System wurde umgestellt auf einen **Email-First Workflow**:
1. User füllt Onboarding aus
2. Verification Email wird versendet
3. User klickt auf Link in Email
4. Email wird verifiziert
5. **Erst dann** werden Meal & Workout Plans generiert

## Workflow

```
User completes Onboarding
    ↓
OnboardingController::store()
    ↓
DB Transaction:
  - User created (email_verified_at = null)
  - UserProfile created
  - Plan created
    ↓
Send OnboardingCompleteVerifyEmail Notification
    ↓
Response: "Please check your email"
    
... USER CHECKS EMAIL ...

User clicks verification link
    ↓
EmailVerificationController::verify()
    ↓
- Validate signature & hash
- Mark email as verified
- Fire EmailVerified Event
    ↓
Listeners:
  - GenerateMealPlan → dispatch GenerateUserMealPlan Job
  - GenerateWorkoutPlan → dispatch GenerateUserWorkoutPlan Job
    ↓
OpenAI generates plans asynchronously
    ↓
Redirect to Frontend: /email-verified
```

## Implementierte Komponenten

### 1. **EmailVerified Event**
- `app/Events/EmailVerified.php`
- Wird nach erfolgreicher Email-Verifizierung gefeuert
- Enthält: `User` und `Plan`

### 2. **OnboardingCompleteVerifyEmail Notification**
- `app/Notifications/OnboardingCompleteVerifyEmail.php`
- Email mit Verifizierungs-Link
- Signed URL (24h gültig)
- Enthält `plan_id` in URL

### 3. **EmailVerificationController**
- `app/Http/Controllers/Api/V2/EmailVerificationController.php`
- `verify()` Methode validiert Link
- Markiert Email als verifiziert
- Feuert `EmailVerified` Event

### 4. **Listener Änderungen**
- `GenerateMealPlan` & `GenerateWorkoutPlan`
- Hören jetzt auf `EmailVerified` statt `OnboardingCompleted`
- Verwenden `__invoke()` statt `handle()`
- Kein `ShouldQueue` mehr (nur Jobs sind queued)

### 5. **User Model**
- Implementiert jetzt `MustVerifyEmail` Interface
- Aktiviert Laravel's Email Verification System

### 6. **Route**
```php
Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');
```

## API Changes

### POST `/api/v2/onboarding`

**Response (Updated):**
```json
{
  "success": true,
  "message": "Onboarding completed successfully. Please check your email to verify your account and start generating your personalized plan.",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "name": "Max Mustermann",
    "email_verified": false
  },
  "profile": {...},
  "next_step": "verify_email"
}
```

**Wichtig:** 
- `email_verified: false` zeigt an, dass Email noch nicht verifiziert ist
- `next_step: "verify_email"` signalisiert nächsten Schritt

### GET `/verify-email/{id}/{hash}?plan_id={plan_id}`

**Parameters:**
- `id` - User ID
- `hash` - SHA1 Hash der Email
- `signature` - Query Parameter (automatisch von Laravel)
- `expires` - Query Parameter (automatisch von Laravel)
- `plan_id` - Plan ID (custom Parameter)

**Success:**
- Redirect zu: `{FRONTEND_URL}/email-verified?plan_id={plan_id}`

**Errors:**
- 403: "Invalid verification link" (Hash stimmt nicht)
- 403: "Verification link expired" (Signature abgelaufen)
- Already verified: Redirect zu `{FRONTEND_URL}/email-already-verified`

## Email Template

Die Notification `OnboardingCompleteVerifyEmail` sendet:

**Subject:** "Verify Your Email - Start Your Fitness Journey!"

**Content:**
- Begrüßung mit User Namen
- Erklärung was generiert wird
- CTA Button: "Verify Email & Generate My Plan"
- Liste der Features (Meals, Workouts, 28 Tage)
- Zeitangabe: "This usually takes just a few minutes"

## Frontend Integration

### 1. **Nach Onboarding**
```typescript
// Response prüfen
const response = await fetch('/api/v2/onboarding', {
  method: 'POST',
  body: JSON.stringify(onboardingData)
});

const data = await response.json();

if (data.next_step === 'verify_email') {
  // Zeige Email Verification Screen
  navigateTo('/check-email');
}
```

### 2. **Email Verification Screen**
```tsx
// /check-email Route
<div>
  <h1>Check Your Email</h1>
  <p>We've sent a verification link to {user.email}</p>
  <p>Click the link to start generating your personalized plan.</p>
  <button onClick={() => resendEmail()}>Resend Email</button>
</div>
```

### 3. **Email Verified Screen**
```tsx
// /email-verified Route
const planId = new URLSearchParams(window.location.search).get('plan_id');

<div>
  <h1>Email Verified! 🎉</h1>
  <p>Your personalized plan is being generated...</p>
  <LoadingSpinner />
  <p>This may take a few minutes.</p>
  <button onClick={() => checkPlanStatus(planId)}>
    Check Status
  </button>
</div>
```

### 4. **Plan Status Polling**
```typescript
const pollPlanStatus = async (planId: number) => {
  const response = await fetch(`/api/v2/plan/status/${planId}`);
  const data = await response.json();
  
  if (data.meal_plans_status === 'generated' && 
      data.workout_plans_status === 'generated') {
    // Redirect to dashboard
    navigateTo('/dashboard');
  } else {
    // Poll again in 5 seconds
    setTimeout(() => pollPlanStatus(planId), 5000);
  }
};
```

## Configuration

### Environment Variables

Füge zur `.env` hinzu:
```env
FRONTEND_URL=http://localhost:3000

# Mail Configuration (already exists)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@fitnessai.app"
MAIL_FROM_NAME="${APP_NAME}"
```

### Config File

`config/app.php`:
```php
'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),
```

## Testing

### Unit Test

```php
test('sends verification email after onboarding', function () {
    Notification::fake();
    
    post('/api/v2/onboarding', [/* ... */])->assertCreated();
    
    $user = User::where('email', 'test@example.com')->first();
    
    Notification::assertSentTo($user, OnboardingCompleteVerifyEmail::class);
    expect($user->email_verified_at)->toBeNull();
});
```

### Manual Testing

1. **Complete Onboarding:**
```bash
curl -X POST http://localhost:8000/api/v2/onboarding \
  -H "Content-Type: application/json" \
  -d '{...}'
```

2. **Check Mailpit (oder Mail Log):**
- Open: http://localhost:8025
- Click email
- Copy verification link

3. **Click Verification Link:**
```bash
curl "http://localhost:8000/verify-email/1/{hash}?signature=...&expires=...&plan_id=1"
```

4. **Check Queue:**
```bash
php artisan queue:work
# Should see: "Generated meal plan for day 1"
```

## Security

### Signed URLs
- Verification Links sind **signed** (tamper-proof)
- **24 Stunden** gültig
- Enthalten SHA1 Hash der Email
- Laravel validiert Signature automatisch

### Protections
- ✅ Hash validation (verhindert User ID Manipulation)
- ✅ Signature validation (verhindert Link Manipulation)
- ✅ Expiration (Links laufen nach 24h ab)
- ✅ Idempotent (Mehrfaches Klicken ist sicher)

## Vorteile des neuen Workflows

1. **Email Validierung**: Stellt sicher, dass User eine gültige Email hat
2. **Spam Prävention**: Verhindert Fake-Registrierungen
3. **Kosten-Kontrolle**: OpenAI wird nur für verifizierte User aufgerufen
4. **User Experience**: Klarer Workflow mit Status-Updates
5. **Marketing**: Email-Liste mit verifizierten Kontakten

## Migration von altem System

**Alt (OnboardingCompleted):**
```php
OnboardingCompleted::dispatch($user, $plan);
→ GenerateMealPlan Listener
→ GenerateWorkoutPlan Listener
```

**Neu (EmailVerified):**
```php
$user->notify(new OnboardingCompleteVerifyEmail($plan));
→ User clicks link
→ EmailVerified::dispatch($user, $plan);
→ GenerateMealPlan Listener
→ GenerateWorkoutPlan Listener
```

## Troubleshooting

### Email wird nicht versendet
- Prüfe `.env` Mail-Konfiguration
- Prüfe Queue Worker läuft: `php artisan queue:work`
- Prüfe Mailpit: http://localhost:8025

### Link funktioniert nicht
- Signature expired → Link älter als 24h
- Invalid hash → User hat Email geändert
- Already verified → User hat bereits geklickt

### Plans werden nicht generiert
- Prüfe ob `EmailVerified` Event gefeuert wird
- Prüfe ob Listener registriert sind
- Prüfe Queue Worker läuft
- Prüfe Logs: `tail -f storage/logs/laravel.log`

---

**Status:** ✅ Email Verification Workflow vollständig implementiert!

