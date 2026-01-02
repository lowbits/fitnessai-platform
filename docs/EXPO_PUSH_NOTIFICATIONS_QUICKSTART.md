# Expo Push Notifications - Quick Start Guide

This integration uses the official [laravel-notification-channels/expo](https://github.com/laravel-notification-channels/expo) package.

## Backend Setup (5 Minutes)

### 1. Install Package (Already Done ✅)
```bash
composer require laravel-notification-channels/expo
```

### 2. Run the Migration
```bash
php artisan migrate
```

This adds the `expo_push_token` column to the `users` table.

### 3. User Model Setup (Already Done ✅)

The User model needs:
- `ExpoPushToken` cast for the `expo_push_token` field
- `routeNotificationForExpo()` method to route notifications

```php
use NotificationChannels\Expo\ExpoPushToken;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'expo_push_token',
        // ...other fields
    ];

    protected function casts(): array
    {
        return [
            'expo_push_token' => ExpoPushToken::class,
        ];
    }

    public function routeNotificationForExpo(): ?ExpoPushToken
    {
        return $this->expo_push_token;
    }
}
```

### 3. Test the API
```bash
# Get your auth token first
TOKEN="your-auth-token-here"

# Test token registration
curl -X POST http://localhost:8000/api/v2/notifications/register-token \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"token":"ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]"}'

# Test token status
curl http://localhost:8000/api/v2/notifications/token-status \
  -H "Authorization: Bearer $TOKEN"

# Send test notification
curl -X POST http://localhost:8000/api/v2/notifications/test \
  -H "Authorization: Bearer $TOKEN"
```

---

## React Native Setup (10 Minutes)

### 1. Install Expo Notifications
```bash
npx expo install expo-notifications expo-device expo-constants
```

### 2. Update app.json
```json
{
  "expo": {
    "plugins": [
      [
        "expo-notifications",
        {
          "icon": "./assets/notification-icon.png",
          "color": "#ffffff",
          "sounds": ["./assets/notification-sound.wav"]
        }
      ]
    ]
  }
}
```

### 3. Create Notification Service

Create `src/services/notifications.ts`:

```typescript
import * as Notifications from 'expo-notifications';
import * as Device from 'expo-device';
import { Platform } from 'react-native';
import Constants from 'expo-constants';
import api from './api';

// Configure notification behavior
Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: true,
    shouldSetBadge: true,
  }),
});

export async function registerForPushNotificationsAsync() {
  let token;

  if (!Device.isDevice) {
    console.log('Must use physical device for Push Notifications');
    return;
  }

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

  token = (await Notifications.getExpoPushTokenAsync({
    projectId: Constants.expoConfig?.extra?.eas?.projectId,
  })).data;

  console.log('Push token:', token);

  if (Platform.OS === 'android') {
    Notifications.setNotificationChannelAsync('default', {
      name: 'default',
      importance: Notifications.AndroidImportance.MAX,
      vibrationPattern: [0, 250, 250, 250],
      lightColor: '#FF231F7C',
    });
  }

  // Register with backend
  try {
    await api.post('/api/v2/notifications/register-token', { token });
    console.log('Token registered with backend');
  } catch (error) {
    console.error('Failed to register token:', error);
  }

  return token;
}

export async function unregisterPushToken() {
  try {
    await api.delete('/api/v2/notifications/remove-token');
    console.log('Token removed from backend');
  } catch (error) {
    console.error('Failed to remove token:', error);
  }
}
```

### 4. Add to Your Login Flow

In your login/auth success handler:

```typescript
import { registerForPushNotificationsAsync } from './services/notifications';

async function handleLoginSuccess() {
  // Your existing login logic
  // ...
  
  // Register for push notifications
  await registerForPushNotificationsAsync();
}
```

### 5. Add to Your Logout Flow

```typescript
import { unregisterPushToken } from './services/notifications';

async function handleLogout() {
  // Unregister push token
  await unregisterPushToken();
  
  // Your existing logout logic
  // ...
}
```

### 6. Handle Notification Interactions

In your main App component:

```typescript
import { useEffect, useRef } from 'react';
import * as Notifications from 'expo-notifications';
import { useNavigation } from '@react-navigation/native';

function App() {
  const navigation = useNavigation();
  const notificationListener = useRef<any>();
  const responseListener = useRef<any>();

  useEffect(() => {
    // Handle foreground notifications
    notificationListener.current = Notifications.addNotificationReceivedListener(
      notification => {
        console.log('Notification received:', notification);
      }
    );

    // Handle notification taps
    responseListener.current = Notifications.addNotificationResponseReceivedListener(
      response => {
        const data = response.notification.request.content.data;
        
        // Navigate based on notification data
        if (data.type === 'workout_reminder' && data.workout_id) {
          navigation.navigate('WorkoutDetail', { 
            workoutId: data.workout_id 
          });
        } else if (data.screen) {
          navigation.navigate(data.screen as never);
        }
      }
    );

    return () => {
      Notifications.removeNotificationSubscription(notificationListener.current);
      Notifications.removeNotificationSubscription(responseListener.current);
    };
  }, []);

  return (
    // Your app components
  );
}
```

---

## Sending Notifications from Backend

### Using Laravel Notifications (Recommended)

Laravel's built-in notification system makes it easy to send push notifications.

### Quick Example: Send After Workout Complete

```php
use App\Notifications\WorkoutCompletedNotification;

class WorkoutTrackingController extends Controller
{
    public function store(Request $request)
    {
        // Store workout tracking
        $tracking = WorkoutTracking::create([...]);
        
        // Send notification
        $request->user()->notify(
            new WorkoutCompletedNotification($tracking->workoutPlan->workout_name)
        );
        
        return response()->json($tracking);
    }
}
```

### Available Notification Classes

```php
use App\Notifications\WorkoutCompletedNotification;
use App\Notifications\WorkoutReminderNotification;
use App\Notifications\PlanGenerationCompletedNotification;
use App\Notifications\WeeklyProgressNotification;

// Workout completed
$user->notify(new WorkoutCompletedNotification('Leg Day'));

// Workout reminder
$user->notify(new WorkoutReminderNotification('Push Day', $workoutId));

// Plan ready
$user->notify(new PlanGenerationCompletedNotification());

// Weekly progress
$user->notify(new WeeklyProgressNotification([
    'workouts_completed' => 5,
    'total_calories' => 2500,
]));
```

### Send to Multiple Users

```php
use Illuminate\Support\Facades\Notification;
use App\Notifications\PlanGenerationCompletedNotification;

// Get users to notify
$users = User::where('active', true)->get();

// Send to all
Notification::send($users, new PlanGenerationCompletedNotification());
```

### Create Custom Notification

```php
php artisan make:notification CustomNotification
```

Then implement:

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class CustomNotification extends Notification
{
    public function via($notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpo($notifiable): ExpoMessage
    {
        return ExpoMessage::create()
            ->title('Custom Title')
            ->body('Custom message body')
            ->data(['key' => 'value'])
            ->channelId('custom')
            ->badge(1)
            ->priority('high');
    }
}
```

---

## Testing

### 1. Test from Backend

Send a test notification:

```bash
curl -X POST http://localhost:8000/api/v2/notifications/test \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. Test from Expo Tool

Visit: https://expo.dev/notifications

Enter your token and send a test notification.

### 3. Test in Development

Use the Expo Go app on a physical device (push notifications don't work in simulator).

---

## Common Issues

### "Must use physical device"
Push notifications don't work in iOS Simulator or Android Emulator. Use a physical device.

### "Token not registered"
Make sure you called `registerForPushNotificationsAsync()` after login.

### "Notifications not showing"
- Check device notification settings
- Ensure app has notification permissions
- Check notification channels are configured (Android)

### "Invalid token format"
Expo tokens start with `ExponentPushToken[` or `ExpoPushToken[` followed by alphanumeric characters.

---

## Next Steps

1. ✅ Run migration
2. ✅ Install expo-notifications in React Native
3. ✅ Add registration to login flow
4. ✅ Add unregister to logout flow
5. ✅ Add notification handlers to App.tsx
6. ✅ Test with test endpoint
7. ✅ Integrate into your features (workout tracking, plan generation, etc.)

For detailed documentation, see: [EXPO_PUSH_NOTIFICATIONS.md](./EXPO_PUSH_NOTIFICATIONS.md)

