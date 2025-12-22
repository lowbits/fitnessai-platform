# Email Verification mit Inertia/Vue - Implementation Guide

## Übersicht

Das Email Verification System verwendet **Inertia.js + Vue 3** für ein nahtloses SPA-Erlebnis ohne externe Redirects.

## Flow

```
User clicks Email Link
    ↓
EmailVerificationController::verify()
    ↓
Inertia renders Vue Page (keine URL redirect!)
    ↓
GeneratingPlan.vue
    ├─ Shows Progress Bars
    ├─ Polls /api/v2/plans/{id}/generation-status
    └─ Auto-redirects to /dashboard when complete
```

## Komponenten

### 1. Controller
`app/Http/Controllers/Api/V2/EmailVerificationController.php`

**Methods:**
- `verify()` - Verifiziert Email, rendert Inertia Page
- `status()` - JSON API für Polling

### 2. Vue Pages

**GeneratingPlan.vue** - Haupt-Seite mit Progress Tracking
- Real-time Progress Bars (Meal Plans & Workouts)
- Polling alle 3 Sekunden
- Auto-redirect zu `/dashboard` wenn fertig
- Error Handling & Retry

**Invalid.vue** - Ungültiger Link
- Zeigt Fehlermeldung
- Button zurück zur Homepage

**Expired.vue** - Abgelaufener Link  
- Zeigt Ablauf-Info
- "Request New Email" Button
- Link zur Homepage

**AlreadyVerified.vue** - Email bereits verifiziert
- Freundliche Nachricht
- "Go to Dashboard" Button

## API Endpoints

### GET `/verify-email/{id}/{hash}`

**Parameters:**
- `id` - User ID
- `hash` - SHA1 hash der Email
- `plan_id` - Query Parameter
- `signature` - Auto von Laravel
- `expires` - Auto von Laravel

**Response:**
- Inertia render (keine JSON)
- Rendert eine der 4 Vue-Seiten

### GET `/api/v2/plans/{planId}/generation-status`

**Response:**
```json
{
  "status": "generating|completed|partial_failure",
  "overall_progress": 45.2,
  "meal_plans": {
    "total": 28,
    "generated": 15,
    "failed": 0,
    "pending": 13,
    "progress": 53.6
  },
  "workout_plans": {
    "total": 28,
    "generated": 10,
    "failed": 0,
    "pending": 18,
    "progress": 35.7
  },
  "is_complete": false,
  "has_failures": false
}
```

## Vue Component Features

### GeneratingPlan.vue

**Props:**
```typescript
{
  user: { id, name, email },
  plan: { id, name, start_date, workouts_per_week }
}
```

**State Management:**
- `status` - Current generation status
- `error` - Error message if fetch fails
- `pollingInterval` - setInterval reference

**Lifecycle:**
- `onMounted` - Start polling
- `onUnmounted` - Stop polling (cleanup)

**Auto-redirect:**
```typescript
if (data.is_complete) {
  stopPolling();
  setTimeout(() => router.visit('/dashboard'), 2000);
}
```

**Visual Features:**
- ✅ Animated progress bars
- ✅ Real-time counters
- ✅ Status indicators (pending/failed)
- ✅ Loading spinners
- ✅ Success checkmark when complete
- ✅ Gradient backgrounds
- ✅ Responsive design

### Styling

**Tailwind Classes:**
- Dark theme (`bg-gray-900`, `bg-gray-800`)
- Gradient backgrounds
- Smooth transitions
- Animated progress bars
- Responsive padding/margins

**Custom Animations:**
```vue
<style scoped>
@keyframes spin {
  to { transform: rotate(360deg); }
}
.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
```

## Testing

### Manual Test Flow

1. **Complete Onboarding:**
```bash
curl -X POST http://localhost:8000/api/v2/onboarding \
  -H "Content-Type: application/json" \
  -d '{ ... }'
```

2. **Check Email (Mailpit):**
```bash
open http://localhost:8025
```

3. **Click Verification Link:**
- Opens in Browser
- Shows GeneratingPlan.vue
- Progress bars update automatically

4. **Watch Queue Worker:**
```bash
php artisan queue:work --verbose
```

5. **Monitor Progress:**
- Progress bars fill up in real-time
- Page auto-redirects to dashboard when done

### Test Different States

**Invalid Link:**
```bash
# Manipulate hash
curl http://localhost:8000/verify-email/1/invalid-hash?...
# Shows Invalid.vue
```

**Expired Link:**
```bash
# Use old signature
curl http://localhost:8000/verify-email/1/{hash}?expires=1609459200...
# Shows Expired.vue
```

**Already Verified:**
```bash
# Click link twice
# Second click shows AlreadyVerified.vue
```

## Development Setup

### 1. Build Assets
```bash
npm run dev
# or for production
npm run build
```

### 2. Start Services
```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Queue Worker
php artisan queue:work

# Terminal 3: Vite (wenn npm run dev)
# Läuft automatisch
```

### 3. Test Email
```bash
# Mailpit für lokales Email-Testing
docker run -d -p 1025:1025 -p 8025:8025 axllent/mailpit
```

## Customization

### Change Polling Interval
```typescript
// GeneratingPlan.vue
pollingInterval = setInterval(fetchStatus, 3000); // 3 seconds
```

### Change Auto-redirect Delay
```typescript
// GeneratingPlan.vue
setTimeout(() => router.visit('/dashboard'), 2000); // 2 seconds
```

### Add More Status Info
```typescript
// EmailVerificationController@status
return response()->json([
  // ...existing fields...
  'estimated_time_remaining': $estimatedMinutes,
  'current_step': 'Generating meal plans...',
]);
```

## Troubleshooting

### Vue Component nicht gefunden
```bash
# Assets neu bauen
npm run build
php artisan cache:clear
```

### Polling funktioniert nicht
- Prüfe Browser Console auf Errors
- Prüfe Network Tab für API Calls
- Prüfe `/api/v2/plans/{id}/generation-status` manuell

### Progress bei 0% stecken
- Prüfe Queue Worker läuft
- Prüfe `jobs` Tabelle für pending jobs
- Prüfe Logs: `tail -f storage/logs/laravel.log`

### Redirect funktioniert nicht
- Prüfe `is_complete` ist `true`
- Prüfe Browser Console Errors
- Prüfe `/dashboard` Route existiert

## Browser Compatibility

**Unterstützt:**
- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers

**Features:**
- ✅ fetch API
- ✅ ES6+ (via Vite)
- ✅ CSS Grid & Flexbox
- ✅ CSS Animations

## Performance

**Optimierungen:**
- Polling nur wenn Seite aktiv
- Cleanup bei unmount
- Lazy loading von Inertia pages
- Minimale Bundle size

**Network:**
- Polling Request: ~1KB
- Page Load: ~50KB (first load)
- Assets: Cached by Vite/Browser

## Security

**Validierungen:**
- ✅ Hash validation (user email)
- ✅ Signature validation (Laravel)
- ✅ Expiration check (24h)
- ✅ CSRF Protection (Inertia)
- ✅ XSS Protection (Vue escaping)

**Rate Limiting:**
```php
// Optional: Add to route
Route::get('/verify-email/{id}/{hash}', ...)
    ->middleware('throttle:6,1'); // 6 requests per minute
```

## File Structure

```
app/
  Http/Controllers/Api/V2/
    EmailVerificationController.php
resources/
  js/Pages/EmailVerification/
    GeneratingPlan.vue       # Main page mit polling
    Invalid.vue              # Invalid link error
    Expired.vue              # Expired link error
    AlreadyVerified.vue      # Already verified success
routes/
  web.php                    # Verification routes
```

## Next Steps

### Enhancements:
- [ ] Add sound notification when complete
- [ ] Add confetti animation on success
- [ ] Show estimated time remaining
- [ ] Add "What's being generated" expandable section
- [ ] Email notification when complete
- [ ] Push notification support

### Analytics:
- [ ] Track verification rate
- [ ] Track time to complete
- [ ] Track failure rate
- [ ] Track user drop-off

---

**Status:** ✅ Inertia/Vue Implementation vollständig!

Das System nutzt jetzt:
- ✅ Seamless SPA Experience (keine externe Redirects)
- ✅ Real-time Progress Tracking
- ✅ Beautiful UI mit Animations
- ✅ Mobile-friendly
- ✅ Auto-redirect when done

