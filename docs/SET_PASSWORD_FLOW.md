# Set Password Flow Documentation

## Overview

This flow allows users who were created without a password (e.g., through social login or onboarding without password) to securely set a password for their account.

## Architecture

### Components

1. **Database Table**: `password_reset_tokens`
   - Stores secure tokens for password setting
   - Tokens expire after 24 hours

2. **Controller**: `SetPasswordController`
   - Handles token generation, verification, and password setting
   - Located at: `app/Http/Controllers/Api/V2/SetPasswordController.php`

3. **Notification**: `SetPasswordNotification`
   - Sends email with deep link and token
   - Supports both English and German translations

## API Endpoints

### 1. Request Set Password Token

**Endpoint**: `POST /api/v2/set-password/request-token`

**Purpose**: Generates and sends a set password token to the user's email.

**Request**:
```json
{
  "email": "user@example.com"
}
```

**Response** (Success):
```json
{
  "success": true,
  "message": "Set password token has been sent to your email."
}
```

**Response** (User already has password):
```json
{
  "success": false,
  "message": "This account already has a password. Please use the forgot password flow instead."
}
```

**Rate Limit**: 5 requests per minute

---

### 2. Verify Token

**Endpoint**: `POST /api/v2/set-password/verify-token`

**Purpose**: Verifies if a token is valid before showing the password form. Useful for UX in mobile app.

**Request**:
```json
{
  "email": "user@example.com",
  "token": "abc123..."
}
```

**Response** (Valid):
```json
{
  "success": true,
  "valid": true,
  "message": "Token is valid.",
  "user": {
    "email": "user@example.com",
    "name": "John Doe"
  }
}
```

**Response** (Invalid/Expired):
```json
{
  "success": true,
  "valid": false,
  "message": "Token has expired."
}
```

**Rate Limit**: 10 requests per minute

---

### 3. Set Password

**Endpoint**: `POST /api/v2/set-password`

**Purpose**: Sets the user's password using the token.

**Request**:
```json
{
  "email": "user@example.com",
  "token": "abc123...",
  "password": "NewSecurePassword123!",
  "password_confirmation": "NewSecurePassword123!"
}
```

**Response** (Success):
```json
{
  "success": true,
  "message": "Password has been set successfully.",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "name": "John Doe"
  },
  "api_token": "1|xyz..."
}
```

**Response** (Invalid Token):
```json
{
  "success": false,
  "message": "Invalid or expired token."
}
```

**Rate Limit**: 5 requests per minute

---

## Mobile App Integration

### React Native Deep Link

The email notification includes a deep link with the following format:

```
fitnessai://set-password?email=user@example.com&token=abc123...
```

### Implementation Steps

1. **Configure Deep Linking** in your React Native app to handle the `fitnessai://` scheme

2. **Handle the Deep Link** in your app:
```javascript
// React Native example
import { Linking } from 'react-native';

Linking.addEventListener('url', handleDeepLink);

function handleDeepLink(event) {
  const url = event.url;
  if (url.startsWith('fitnessai://set-password')) {
    const params = parseQueryString(url);
    navigation.navigate('SetPassword', {
      email: params.email,
      token: params.token
    });
  }
}
```

3. **Verify Token** before showing the form:
```javascript
const verifyToken = async (email, token) => {
  const response = await fetch('/api/v2/set-password/verify-token', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, token })
  });
  
  const data = await response.json();
  return data.valid;
};
```

4. **Submit Password**:
```javascript
const setPassword = async (email, token, password, passwordConfirmation) => {
  const response = await fetch('/api/v2/set-password', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      email,
      token,
      password,
      password_confirmation: passwordConfirmation
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    // Store the API token
    await AsyncStorage.setItem('api_token', data.api_token);
    // Navigate to authenticated area
    navigation.navigate('Dashboard');
  }
};
```

---

## Automatic Token Sending

When a user completes onboarding without providing a password, a set password token is automatically generated and sent via email.

This happens in `OnboardingController::store()`:

```php
// If user was created without password, send set password token
if (!$result['has_password']) {
    $this->sendSetPasswordToken($result['user']);
}
```

---

## Security Features

1. **Token Hashing**: Tokens are hashed using bcrypt before storage
2. **Expiration**: Tokens expire after 24 hours
3. **Rate Limiting**: All endpoints have rate limiting to prevent abuse
4. **One-Time Use**: Tokens are deleted after successful password setting
5. **Password Requirements**: Uses Laravel's Password validation rules
6. **Pre-existing Password Check**: Prevents setting password if one already exists

---

## Email Notifications

### Languages Supported
- English (en)
- German (de)

### Email Content
- Subject: "Set Your Password" / "Setze dein Passwort"
- Contains both deep link and plain token
- Expires in 24 hours
- Localized based on user's preferred language

---

## Configuration

### Deep Link Scheme
You can customize the deep link scheme in `SetPasswordNotification`:

```php
// Change 'fitnessai' to your app's scheme
return 'fitnessai://set-password?' . $params;
```

### Token Expiration
Default is 24 hours. To change:

```php
// In SetPasswordController
if (now()->diffInHours($tokenRecord->created_at) > 24) {
    // Change 24 to desired hours
}
```

---

## Testing

### Manual Testing Flow

1. Create a user without password through onboarding
2. Check email for set password link
3. Click link or use token in mobile app
4. Verify token is valid
5. Set password
6. Verify user can now log in with password

### API Testing with cURL

```bash
# Request token
curl -X POST http://your-app.test/api/v2/set-password/request-token \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com"}'

# Verify token
curl -X POST http://your-app.test/api/v2/set-password/verify-token \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","token":"your-token-here"}'

# Set password
curl -X POST http://your-app.test/api/v2/set-password \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","token":"your-token-here","password":"NewPassword123!","password_confirmation":"NewPassword123!"}'
```

---

## Database Migration

Run the migration to create the `password_reset_tokens` table:

```bash
php artisan migrate
```

---

## Error Handling

The API returns appropriate HTTP status codes:
- `200`: Success
- `400`: Invalid request (e.g., user already has password, expired token)
- `404`: User not found
- `429`: Rate limit exceeded

Always check the `success` field in the JSON response.

---

## Future Enhancements

Potential improvements:
- SMS-based token delivery
- Token refresh endpoint
- Custom token expiration per user
- Admin panel to manually send set password tokens
- Webhook notifications when password is set

