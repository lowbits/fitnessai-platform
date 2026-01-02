# Expo Push Notifications Integration

## Overview
This document describes the Expo Push Notifications integration with the Laravel backend using the official [laravel-notification-channels/expo](https://github.com/laravel-notification-channels/expo) package.

## Package Installation

```bash
composer require laravel-notification-channels/expo
```

## Database Changes

### Migration
A single column is added to the `users` table:

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('expo_push_token')->nullable()->after('locale');
});
```

**Note**: This approach stores one token per user. If you need multi-device support, you can create a separate `user_devices` table with multiple tokens.

## API Endpoints

### Base URL: `/api/v2/notifications`

All endpoints require `auth:sanctum` authentication.

### 1. Register Push Token
**POST** `/api/v2/notifications/register-token`

Register or update the user's Expo push token.

**Request Body:**
```json
{
  "token": "ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]"
}
```

**Response (200):**
```json
{
  "message": "Push token registered successfully",
  "token": "ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]"
}
```

**Response (422):**
```json
{
  "error": "Validation failed",
  "message": "Invalid push token format",
  "errors": {
    "token": ["The token format is invalid."]
  }
}
```

---

### 2. Remove Push Token
**DELETE** `/api/v2/notifications/remove-token`

Remove the user's push token (e.g., on logout).

**Response (200):**
```json
{
  "message": "Push token removed successfully"
}
```

---

### 3. Get Token Status
**GET** `/api/v2/notifications/token-status`

Get the current user's push token status.

**Response (200):**
```json
{
  "has_token": true,
  "token": "ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]",
  "updated_at": "2026-01-02T10:30:00.000000Z"
}
```

---

### 4. Send Test Notification
**POST** `/api/v2/notifications/test`

Send a test notification to the current user.

**Response (200):**
```json
{
  "message": "Test notification sent successfully"
}
```

**Response (400):**
```json
{
  "error": "No push token registered",
  "message": "Please register a push token first"
}
```

---

## Usage in React Native

### 1. Register Token After Login

```typescript
import * as Notifications from 'expo-notifications';
import { Platform } from 'react-native';
import api from './api'; // Your API client

async function registerForPushNotifications() {
  // Request permissions
  const { status: existingStatus } = await Notifications.getPermissionsAsync();
  let finalStatus = existingStatus;
  
  if (existingStatus !== 'granted') {
    const { status } = await Notifications.requestPermissionsAsync();
    finalStatus = status;
  }
  
  if (finalStatus !== 'granted') {
    console.log('Permission not granted for push notifications');
    return;
  }

  // Get the token
  const token = (await Notifications.getExpoPushTokenAsync({
    projectId: 'your-project-id', // From app.json
  })).data;

  // Register with backend
  try {
    await api.post('/api/v2/notifications/register-token', { token });
    console.log('Push token registered:', token);
  } catch (error) {
    console.error('Failed to register push token:', error);
  }

  // Configure Android notification channel
  if (Platform.OS === 'android') {
    await Notifications.setNotificationChannelAsync('default', {
      name: 'Default',
      importance: Notifications.AndroidImportance.MAX,
      vibrationPattern: [0, 250, 250, 250],
      lightColor: '#FF231F7C',
    });
  }

  return token;
}
```

### 2. Remove Token on Logout

```typescript
async function logout() {
  try {
    // Remove token from backend
    await api.delete('/api/v2/notifications/remove-token');
    
    // Clear local auth data
    await AsyncStorage.removeItem('authToken');
    
    // Navigate to login screen
    navigation.navigate('Login');
  } catch (error) {
    console.error('Logout error:', error);
  }
}
```

### 3. Handle Incoming Notifications

```typescript
import { useEffect, useRef } from 'react';
import * as Notifications from 'expo-notifications';
import { useNavigation } from '@react-navigation/native';

// Configure notification behavior
Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: true,
    shouldSetBadge: true,
  }),
});

function App() {
  const navigation = useNavigation();
  const notificationListener = useRef();
  const responseListener = useRef();

  useEffect(() => {
    // Handle notifications received while app is foregrounded
    notificationListener.current = Notifications.addNotificationReceivedListener(notification => {
      console.log('Notification received:', notification);
    });

    // Handle notification interactions (user tapped on notification)
    responseListener.current = Notifications.addNotificationResponseReceivedListener(response => {
      const data = response.notification.request.content.data;
      
      // Navigate based on notification type
      if (data.type === 'workout_reminder' && data.workout_id) {
        navigation.navigate('WorkoutDetail', { workoutId: data.workout_id });
      } else if (data.screen) {
        navigation.navigate(data.screen);
      }
    });

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

## Backend Usage

### User Model Setup

Add the `ExpoPushToken` cast and routing method to your User model:

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

    /**
     * Route notifications for the Expo channel.
     */
    public function routeNotificationForExpo(): ?ExpoPushToken
    {
        return $this->expo_push_token;
    }
}
```

### Managing Push Tokens

```php
// Register/update a token
$user->update(['expo_push_token' => 'ExponentPushToken[xxx]']);

// Check if user has a token
if ($user->expo_push_token) {
    // User has a token
}

// Remove token (e.g., on logout)
$user->update(['expo_push_token' => null]);

// Get token as string
$tokenString = $user->expo_push_token?->toString();
```

### Creating Notifications

Create a notification class:

```php
php artisan make:notification WorkoutReminderNotification
```

Implement the notification:

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class WorkoutReminderNotification extends Notification
{
    public function __construct(
        private string $workoutName,
        private int $workoutId
    ) {}

    public function via($notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpo($notifiable): ExpoMessage
    {
        return ExpoMessage::create()
            ->title('🏋️ Workout Reminder')
            ->body("Time for your workout: {$this->workoutName}")
            ->data([
                'type' => 'workout_reminder',
                'workout_id' => $this->workoutId,
                'screen' => 'WorkoutDetail',
            ])
            ->channelId('workouts')
            ->badge(1)
            ->priority('high');
    }
}
```

### Sending Notifications

```php
use App\Notifications\WorkoutReminderNotification;

// Send to single user
$user->notify(new WorkoutReminderNotification('Push Day', 123));

// Send to multiple users
use Illuminate\Support\Facades\Notification;

$users = User::where('active', true)->get();
Notification::send($users, new WorkoutReminderNotification('Push Day', 123));
```

### Available ExpoMessage Methods

```php
ExpoMessage::create()
    ->title('Title')                          // Notification title
    ->body('Body text')                       // Notification body
    ->data(['key' => 'value'])                // Custom data payload
    ->channelId('channel_id')                 // Android notification channel
    ->badge(1)                                // iOS badge number
    ->priority('high')                        // Priority: default, high
    ->sound('default')                        // Sound: default or null
    ->ttl(3600);                              // Time to live in seconds
```

### Pre-built Notification Classes

#### 1. WorkoutCompletedNotification
```php
use App\Notifications\WorkoutCompletedNotification;

$user->notify(new WorkoutCompletedNotification('Leg Day'));
```

#### 2. WorkoutReminderNotification
```php
use App\Notifications\WorkoutReminderNotification;

$user->notify(new WorkoutReminderNotification('Push Day', $workoutId));
```

#### 3. PlanGenerationCompletedNotification
```php
use App\Notifications\PlanGenerationCompletedNotification;

$user->notify(new PlanGenerationCompletedNotification());
```

#### 4. WeeklyProgressNotification
```php
use App\Notifications\WeeklyProgressNotification;

$user->notify(new WeeklyProgressNotification([
    'workouts_completed' => 5,
    'total_calories' => 2500,
]));
```

### Example Integrations

#### In a Job (Plan Generation)
```php
use App\Notifications\PlanGenerationCompletedNotification;

class GeneratePlanJob
{
    public function handle()
    {
        // Generate plan logic
        $plan->update(['generation_completed_at' => now()]);
        
        // Notify user
        $plan->user->notify(new PlanGenerationCompletedNotification());
    }
}
```

#### In a Controller (Workout Tracking)
```php
use App\Notifications\WorkoutCompletedNotification;

class WorkoutTrackingController extends Controller
{
    public function store(Request $request)
    {
        $tracking = WorkoutTracking::create([...]);
        
        // Send notification
        $request->user()->notify(
            new WorkoutCompletedNotification($tracking->workoutPlan->workout_name)
        );
        
        return response()->json($tracking);
    }
}
```

#### In a Scheduled Command (Workout Reminders)
```php
use App\Notifications\WorkoutReminderNotification;

class SendWorkoutReminders extends Command
{
    public function handle()
    {
        $workoutsToday = WorkoutPlan::whereDate('date', today())
            ->with('plan.user')
            ->get();

        foreach ($workoutsToday as $workout) {
            $workout->plan->user->notify(
                new WorkoutReminderNotification(
                    $workout->workout_name,
                    $workout->id
                )
            );
        }
    }
}
```

---

## Notification Channels

Different notification types use different channels for better organization on Android:

- `default` - Default notifications
- `workouts` - Workout reminders
- `plans` - Plan generation and updates
- `achievements` - Workout completions, milestones
- `progress` - Weekly/monthly summaries
- `general` - Generic notifications

Configure these in your React Native app using `Notifications.setNotificationChannelAsync()`.

---

## Security & Best Practices

1. **Token Validation**: Tokens are validated with regex before storage
2. **User Isolation**: Users can only register/remove their own tokens
3. **Automatic Cleanup**: Remove tokens on logout
4. **Error Handling**: All API calls have proper error handling and logging
5. **Token Expiry**: Track `expo_push_token_updated_at` for token freshness
6. **Rate Limiting**: Consider adding rate limiting to notification endpoints

---

## Testing

### Test Endpoint
Use the test endpoint to verify the integration:

```bash
curl -X POST https://your-api.com/api/v2/notifications/test \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### Manual Test with Expo
You can test push notifications directly from [Expo Push Notification Tool](https://expo.dev/notifications).

---

## Troubleshooting

### Token Not Working
- Ensure the token format is correct: `ExponentPushToken[xxx]` or `ExpoPushToken[xxx]`
- Check that the user has granted notification permissions
- Verify the projectId in `app.json` matches your Expo project

### Notifications Not Received
- Check app is not in development mode (push notifications don't work in Expo Go for production apps)
- Verify the user's push token is registered in the database
- Check Laravel logs for any errors when sending notifications
- Ensure Android notification channels are properly configured

### Multiple Tokens
- Users might have tokens from multiple devices
- Consider storing tokens in a separate `push_tokens` table for multi-device support

---

## Future Enhancements

1. **Multi-device Support**: Store multiple tokens per user
2. **Notification Preferences**: Let users control which notifications they receive
3. **Scheduled Notifications**: Schedule workout reminders at specific times
4. **Rich Notifications**: Add images and actions to notifications
5. **Analytics**: Track notification delivery and engagement rates

---

## References

- [Expo Push Notifications](https://docs.expo.dev/push-notifications/overview/)
- [Expo Push Notification Tool](https://expo.dev/notifications)
- [Laravel Notifications](https://laravel.com/docs/notifications)

