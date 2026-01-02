# Multi-Device Push Notifications - Updated Implementation

## ✅ What's New

### Multi-Device Support
Users can now register multiple devices (phone, tablet, etc.) and receive push notifications on all of them!

### Improvements
- ✅ Uses `ExpoPushToken::rule()` for validation (official package method)
- ✅ `device_id` support for tracking multiple devices per user
- ✅ Separate `user_devices` table for better organization
- ✅ Optional device metadata (name, platform)
- ✅ Remove specific device or all devices
- ✅ Backward compatible with single token on user table

## Database Schema

### New Table: `user_devices`
```sql
CREATE TABLE user_devices (
    id BIGINT PRIMARY KEY,
    user_id BIGINT (FK to users),
    device_id VARCHAR(255) UNIQUE,
    expo_push_token VARCHAR(255),
    device_name VARCHAR(255) NULL,
    platform VARCHAR(255) NULL,
    last_used_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Note**: This is a clean multi-device only implementation. No `expo_push_token` column on the users table.

## API Endpoints

### 1. Register Device Token
**POST** `/api/v2/notifications/register-token`

Register a device with its push token. If the device already exists, it will be updated.

**Request:**
```json
{
  "device_id": "unique-device-identifier",
  "token": "ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]",
  "device_name": "John's iPhone",
  "platform": "ios"
}
```

**Validation:**
- `device_id` (required): Unique identifier for the device (2-255 chars)
- `token` (required): Expo push token (validated with `ExpoPushToken::rule()`)
- `device_name` (optional): Human-readable device name
- `platform` (optional): `ios` or `android`

**Response (200):**
```json
{
  "message": "Push token registered successfully",
  "device": {
    "id": 1,
    "device_id": "unique-device-identifier",
    "device_name": "John's iPhone",
    "platform": "ios"
  }
}
```

---

### 2. Remove Device Token
**DELETE** `/api/v2/notifications/remove-token`

Remove a specific device or all devices.

**Request (Remove specific device):**
```json
{
  "device_id": "unique-device-identifier"
}
```

**Request (Remove all devices):**
```json
{}
```

**Response (200) - Specific device:**
```json
{
  "message": "Device token removed successfully",
  "device_id": "unique-device-identifier"
}
```

**Response (200) - All devices:**
```json
{
  "message": "All push tokens removed successfully",
  "devices_removed": 3
}
```

---

### 3. Get Token Status
**GET** `/api/v2/notifications/token-status`

Get all registered devices for the current user.

**Response (200):**
```json
{
  "has_token": true,
  "devices": [
    {
      "id": 1,
      "device_id": "iphone-13-pro",
      "device_name": "John's iPhone",
      "platform": "ios",
      "last_used_at": "2026-01-02T12:00:00.000000Z",
      "created_at": "2026-01-01T10:00:00.000000Z"
    },
    {
      "id": 2,
      "device_id": "ipad-air",
      "device_name": "John's iPad",
      "platform": "ios",
      "last_used_at": "2026-01-02T11:30:00.000000Z",
      "created_at": "2026-01-01T11:00:00.000000Z"
    }
  ],
  "device_count": 2
}
```

---

### 4. Send Test Notification
**POST** `/api/v2/notifications/test`

Send a test notification to all user's devices.

**Response (200):**
```json
{
  "message": "Test notification sent successfully",
  "sent_to_devices": 2
}
```

## React Native Implementation

### 1. Generate Device ID

```typescript
import * as Device from 'expo-device';
import * as Application from 'expo-application';
import { Platform } from 'react-native';

async function getDeviceId(): Promise<string> {
  if (Platform.OS === 'ios') {
    // Use identifierForVendor on iOS
    return await Application.getIosIdForVendorAsync() || 'unknown-ios';
  } else {
    // Use androidId on Android
    return Application.androidId || 'unknown-android';
  }
}
```

### 2. Register Token with Device Info

```typescript
import * as Notifications from 'expo-notifications';
import * as Device from 'expo-device';
import { Platform } from 'react-native';
import api from './api';

async function registerForPushNotifications() {
  if (!Device.isDevice) {
    console.log('Must use physical device for Push Notifications');
    return;
  }

  // Request permissions
  const { status: existingStatus } = await Notifications.getPermissionsAsync();
  let finalStatus = existingStatus;
  
  if (existingStatus !== 'granted') {
    const { status } = await Notifications.requestPermissionsAsync();
    finalStatus = status;
  }
  
  if (finalStatus !== 'granted') {
    alert('Failed to get push token for push notification!');
    return;
  }

  // Get the token
  const token = (await Notifications.getExpoPushTokenAsync({
    projectId: Constants.expoConfig?.extra?.eas?.projectId,
  })).data;

  // Get device info
  const deviceId = await getDeviceId();
  const deviceName = Device.deviceName || Device.modelName;
  const platform = Platform.OS;

  // Register with backend
  try {
    const response = await api.post('/api/v2/notifications/register-token', {
      device_id: deviceId,
      token: token,
      device_name: deviceName,
      platform: platform,
    });
    
    console.log('Push token registered:', response.data);
    return token;
  } catch (error) {
    console.error('Failed to register push token:', error);
  }
}
```

### 3. Remove Token on Logout

```typescript
async function logout() {
  try {
    const deviceId = await getDeviceId();
    
    // Remove only this device
    await api.delete('/api/v2/notifications/remove-token', {
      data: { device_id: deviceId }
    });
    
    // Or remove all devices
    // await api.delete('/api/v2/notifications/remove-token');
    
    // Clear local auth data
    await AsyncStorage.removeItem('authToken');
    navigation.navigate('Login');
  } catch (error) {
    console.error('Logout error:', error);
  }
}
```

### 4. Check Device Registration Status

```typescript
async function checkDeviceStatus() {
  try {
    const response = await api.get('/api/v2/notifications/token-status');
    
    console.log('Registered devices:', response.data.devices);
    console.log('Total devices:', response.data.device_count);
    
    return response.data;
  } catch (error) {
    console.error('Failed to get device status:', error);
  }
}
```

## Backend Usage

### Sending Notifications (Automatic Multi-Device)

```php
use App\Notifications\WorkoutCompletedNotification;

// Send to user (automatically sends to all their devices)
$user->notify(new WorkoutCompletedNotification('Leg Day'));

// The notification system automatically calls routeNotificationForExpo()
// which returns Collection<int, ExpoPushToken> with all device tokens
```

### User Model Implementation

```php
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Route notifications for the Expo channel.
     *
     * @return Collection<int, \NotificationChannels\Expo\ExpoPushToken>
     */
    public function routeNotificationForExpo(): Collection
    {
        return $this->devices->pluck('expo_push_token');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }
}
```

### Check User's Devices

```php
// Get all user's devices
$devices = $user->devices;

// Count devices
$deviceCount = $user->devices()->count();

// Get devices by platform
$iosDevices = $user->devices()->where('platform', 'ios')->get();
$androidDevices = $user->devices()->where('platform', 'android')->get();

// Update last used
$device = $user->devices()->where('device_id', $deviceId)->first();
$device->update(['last_used_at' => now()]);
```

### Clean Up Inactive Devices

```php
// Remove devices not used in 90 days
UserDevice::where('last_used_at', '<', now()->subDays(90))->delete();

// Or create a scheduled command
class CleanupInactiveDevices extends Command
{
    public function handle()
    {
        $deleted = UserDevice::where('last_used_at', '<', now()->subDays(90))
            ->delete();
            
        $this->info("Deleted {$deleted} inactive devices");
    }
}
```

## Migration

Run the migration:
```bash
php artisan migrate
```

This will create the `user_devices` table.

## Benefits

✅ **Multi-Device Support**: Users receive notifications on all their devices
✅ **Device Management**: Track and manage individual devices
✅ **Selective Removal**: Remove specific device or all devices
✅ **Device Metadata**: Store device name and platform for better UX
✅ **Last Used Tracking**: Know when each device was last active
✅ **Backward Compatible**: Still supports single token on user table
✅ **Official Validation**: Uses `ExpoPushToken::rule()` from the package
✅ **Type Safe**: ExpoPushToken cast handles validation automatically

## Example: Device Management UI

You could build a "Manage Devices" screen showing:
- List of all registered devices
- Last used timestamp
- Platform icons (iOS/Android)
- "Remove Device" button for each
- "Remove All Devices" button

This gives users control over where they receive notifications!

