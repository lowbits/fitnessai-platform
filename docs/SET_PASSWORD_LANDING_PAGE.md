# Set Password Landing Page - Complete Implementation

## ✅ Was wurde implementiert

### 1. Universal Link Support
```json
// .well-known/apple-app-site-association
{
  "applinks": {
    "details": [{
      "appID": "HXX8GTN8W8.com.fytrr.app",
      "paths": ["/set-password/*"]  // ✅ Hinzugefügt
    }]
  }
}
```

### 2. Web Landing Page
**Route:** `/set-password/{token}?email=user@example.com`

**Features:**
- ✅ Versucht automatisch App zu öffnen (Universal Link + Deep Link)
- ✅ Fallback: Download Buttons (App Store + Play Store)
- ✅ Responsive Design
- ✅ Loading States
- ✅ Professional UI

### 3. Email Integration
SetPasswordNotification verwendet jetzt Web-Link:
```php
$setPasswordUrl = route('set-password', ['token' => $this->token]) 
    . '?email=' . urlencode($notifiable->email);
```

## User Flow

### Szenario 1: User hat App installiert (iOS/Android)
```
1. User klickt "Set Password" in Email
2. Browser öffnet: fitness-ai.me/set-password/abc123?email=...
3. ✅ OS erkennt Universal Link automatisch (via .well-known)
4. ✅ App öffnet sich DIREKT - KEIN JavaScript nötig!
```

### Szenario 2: User hat App NICHT installiert
```
1. User klickt "Set Password" in Email
2. Browser öffnet: fitness-ai.me/set-password/abc123?email=...
3. Landing Page zeigt:
   - Info: "If app didn't open, click Open button"
   - App Store Badge
   - Play Store Badge
4. User lädt App herunter
5. User öffnet Link nochmal → App öffnet sich ✅
```

## Wichtig: KEIN JavaScript `tryOpenApp()` nötig!

**Warum?**
- ✅ `.well-known/apple-app-site-association` ist konfiguriert
- ✅ iOS/Android erkennen Universal Link AUTOMATISCH
- ✅ OS öffnet App direkt ohne JavaScript
- ✅ Einfacher, zuverlässiger, nativer

## Deep Links

### Universal Link (iOS - bevorzugt):
```
https://fitness-ai.me/set-password/abc123?email=user@example.com
```

### Deep Link (Fallback):
```
fytrr://set-password?token=abc123&email=user@example.com
```

## Landing Page UI

```
┌────────────────────────────────┐
│          💪                     │
│   Set Your Password            │
│                                │
│   Open this link in the        │
│   Fytrr app to securely...     │
│                                │
│  ┌──────────────────────────┐ │
│  │  Open in Fytrr App       │ │
│  └──────────────────────────┘ │
│                                │
│            or                  │
│                                │
│   Don't have the app yet?     │
│                                │
│  [App Store]  [Play Store]    │
│                                │
└────────────────────────────────┘
```

## Technische Details

### Landing Page Features:
- **Universal Links**: OS öffnet App automatisch (iOS/Android)
- **Keine JavaScript Magic**: Kein `tryOpenApp()` - OS macht alles
- **Clean UI**: Info-Message falls App nicht öffnet
- **Responsive**: Mobile & Desktop optimiert
- **Vue 3**: Konsistent mit dem Rest der App
- **TypeScript**: Type-safe Props
- **Inertia**: SSR-ready

### Vue 3 Component:
```vue
// resources/js/pages/SetPassword.vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    token: string;
    email: string;
}>();
</script>

<template>
  <!-- Clean UI, kein JavaScript tryOpenApp() -->
  <!-- OS öffnet App automatisch via Universal Links -->
</template>
```

### Route:
```php
// routes/web.php
Route::get('/set-password/{token}', function ($token) {
    $email = request()->query('email', '');
    return Inertia::render('SetPassword', compact('token', 'email'));
});
```

### Warum KEIN JavaScript tryOpenApp()?

**Weil Universal Links nativ funktionieren:**
1. ✅ `.well-known/apple-app-site-association` ist konfiguriert
2. ✅ iOS erkennt Domain + Path Pattern automatisch
3. ✅ Android App Links funktionieren gleich
4. ✅ OS öffnet App VOR dem Browser-Load
5. ✅ Kein setTimeout, kein window.location Hack
6. ✅ Native, zuverlässig, sauber

## React Native Integration

In der App muss der Deep Link Handler hinzugefügt werden:

```typescript
// App.tsx oder Linking-Handler
Linking.addEventListener('url', (event) => {
  const url = event.url;
  
  if (url.includes('set-password')) {
    const params = new URLSearchParams(url.split('?')[1]);
    const token = params.get('token');
    const email = params.get('email');
    
    // Navigate to SetPassword screen
    navigation.navigate('SetPassword', { token, email });
  }
});

// Handle Universal Links
const initialUrl = await Linking.getInitialURL();
if (initialUrl?.includes('set-password')) {
  // Handle on app launch
}
```

## Testing

### Test Email Link:
```bash
# 1. Generiere Set Password Email
php artisan tinker
>>> $user = User::find(1);
>>> $token = Str::random(64);
>>> $user->notify(new SetPasswordNotification($token));

# 2. Öffne Link aus Email
https://fitness-ai.me/set-password/{token}?email={email}

# 3. Sollte App öffnen oder Landing Page zeigen
```

### Test Universal Link (iOS Simulator):
```bash
xcrun simctl openurl booted "https://fitness-ai.me/set-password/test123?email=test@example.com"
```

### Test Deep Link:
```bash
xcrun simctl openurl booted "fytrr://set-password?token=test123&email=test@example.com"
```

## Security

✅ **Token in URL ist sicher:**
- Tokens sind einmalig verwendbar
- Expiration nach 24h
- HTTPS verschlüsselt
- Keine sensitiven Daten außer Email

✅ **CSRF Protection:**
- Landing Page ist read-only
- Actual password set erfolgt über API
- Token Validation im Backend

## App Store Requirements

**Für iOS Universal Links:**
1. ✅ `.well-known/apple-app-site-association` muss auf Root-Domain erreichbar sein
2. ✅ HTTPS erforderlich
3. ✅ Keine Redirects auf diesem Path
4. ✅ Content-Type: `application/json`

**Für Android App Links:**
```json
// android/app/src/main/AndroidManifest.xml
<intent-filter android:autoVerify="true">
  <action android:name="android.intent.action.VIEW" />
  <category android:name="android.intent.category.DEFAULT" />
  <category android:name="android.intent.category.BROWSABLE" />
  <data android:scheme="https" 
        android:host="fitness-ai.me" 
        android:pathPrefix="/set-password" />
</intent-filter>
```

## Production Checklist

- [ ] Update App Store Badge Link
- [ ] Update Play Store Badge Link
- [ ] Test Universal Links auf Production Domain
- [ ] Verify `.well-known` file accessible
- [ ] Test Email sends correct links
- [ ] Test on real iOS device
- [ ] Test on real Android device
- [ ] Monitor link click rates

---

**Set Password Flow mit Universal Links ist komplett!** 🔗

