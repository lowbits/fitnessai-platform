# Set Password with Signed URL - Security Enhancement

## ✅ Implementiert

### Was wurde hinzugefügt?

Signed URLs für die `/set-password` Route, um sicherzustellen dass:
- Links nach 24 Stunden ablaufen
- Links nicht manipuliert werden können
- Nur authentische Links funktionieren

## Änderungen

### 1. SetPasswordNotification.php
```php
// Vorher: Einfache Route mit Query-Parameter
$setPasswordUrl = route('set-password', ['token' => $this->token]) 
    . '?email=' . urlencode($notifiable->email);

// Nachher: Signed URL (temporär, 24h)
$setPasswordUrl = \URL::temporarySignedRoute(
    'set-password',
    now()->addHours(24),
    [
        'token' => $this->token,
        'email' => $notifiable->email,
    ]
);
```

### 2. routes/web.php
```php
Route::get('/{locale}/set-password/{token}', function ($token) {
    // ...
})
    ->middleware(['signed'])  // ✅ Validiert Signatur
    ->name('set-password');
```

### 3. apple-app-site-association
```json
{
  "applinks": {
    "details": [{
      "paths": ["/*/set-password/*"]  // ✅ Mit Locale-Prefix
    }]
  }
}
```

## Sicherheitsvorteile

### ✅ **Ablauf nach 24 Stunden**
```
Link erstellt: 2026-01-03 10:00
Link gültig bis: 2026-01-04 10:00
Nach 10:00: HTTP 403 Forbidden
```

### ✅ **Manipulations-Schutz**
```
Original: /en/set-password/abc123?signature=xyz123&expires=...
Manipuliert: /en/set-password/hacked?signature=xyz123&expires=...
→ HTTP 403 Forbidden (Signatur ungültig)
```

### ✅ **Token + Email Binding**
Die Signatur inkludiert:
- Token
- Email
- Expiration Time

Änderung eines Parameters = ungültige Signatur

## URL Format

### Generierte URL:
```
https://fitness-ai.me/en/set-password/{token}
  ?signature={hash}
  &expires={timestamp}
  &email={email}
```

### Beispiel:
```
https://fitness-ai.me/en/set-password/abc123def456
  ?signature=a1b2c3d4e5f6...
  &expires=1735996800
  &email=user@example.com
```

## Fehler-Handling

### Link abgelaufen:
```
HTTP 403 Forbidden
"This link has expired"
```

### Signatur ungültig:
```
HTTP 403 Forbidden
"Invalid signature"
```

### Lösung:
User muss neue Set-Password Email anfordern.

## Universal Links

✅ **Funktionieren weiterhin:**
```
1. User klickt Email Link (Signed URL)
2. iOS/Android erkennt Domain + Path Pattern
3. App öffnet sich
4. App kann Token + Email aus URL extrahieren
5. App validiert Token mit Backend API
```

**Wichtig:** Die Signatur muss NICHT in der App validiert werden - nur Backend validiert die Signatur beim ersten Request.

## Testing

### Test Signed URL Generierung:
```bash
php artisan tinker
>>> $user = User::find(1);
>>> $token = Str::random(64);
>>> $user->notify(new SetPasswordNotification($token));
# Check logs für URL
```

### Test URL Validation:
```bash
# Gültige URL (aus Email kopieren)
curl "https://fitness-ai.me/en/set-password/abc123?signature=..."

# Manipulierte URL (ändere Token)
curl "https://fitness-ai.me/en/set-password/hacked?signature=..."
# → 403 Forbidden

# Abgelaufene URL (warte 24h oder ändere expires)
curl "https://fitness-ai.me/en/set-password/abc123?signature=...&expires=123456"
# → 403 Forbidden
```

## React Native Integration

Die App muss nichts Besonderes für Signed URLs tun:

```typescript
// Deep Link Handler
Linking.addEventListener('url', (event) => {
  const url = event.url;
  
  if (url.includes('set-password')) {
    const urlObj = new URL(url);
    const token = urlObj.pathname.split('/').pop();
    const email = urlObj.searchParams.get('email');
    
    // Navigate to SetPassword screen
    // Backend validiert Signatur automatisch
    navigation.navigate('SetPassword', { token, email });
  }
});
```

## Vorteile vs. Vorher

| Feature | Vorher | Nachher |
|---------|--------|---------|
| **Ablauf** | ❌ Nie | ✅ 24 Stunden |
| **Manipulation** | ❌ Möglich | ✅ Unmöglich |
| **Sicherheit** | ⚠️ Basic | ✅ Signiert |
| **Universal Links** | ✅ Ja | ✅ Ja |
| **User Experience** | ✅ Gut | ✅ Gleich gut |

## Best Practices

✅ **DO:**
- Signed URLs für sensitive Actions (Set Password, Email Verify)
- Kurze Expiration (24h)
- Log Signed URL Generation in Development

❌ **DON'T:**
- Signed URLs in Frontend Code einbauen
- Expiration Time im Frontend validieren
- Signatur-Algorithmus im Frontend implementieren

---

**Set Password ist jetzt mit Signed URLs gesichert!** 🔐

