<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use App\Notifications\WorkoutCompletedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use NotificationChannels\Expo\ExpoPushToken;

class PushNotificationController extends Controller
{
    /**
     * Register or update user's Expo push token with device
     */
    public function registerToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_id' => ['required', 'string', 'min:2', 'max:255'],
            'token' => ['required', ExpoPushToken::rule()],
            'device_name' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'in:ios,android'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => 'Invalid input',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Update or create device
        $device = UserDevice::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_id' => $request->input('device_id'),
            ],
            [
                'expo_push_token' => $request->input('token'),
                'device_name' => $request->input('device_name'),
                'platform' => $request->input('platform'),
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Push token registered successfully',
            'device' => [
                'id' => $device->id,
                'device_id' => $device->device_id,
                'device_name' => $device->device_name,
                'platform' => $device->platform,
            ],
        ]);
    }

    /**
     * Remove user's push token (e.g., on logout)
     * If device_id is provided, removes only that device
     * Otherwise, removes all devices
     */
    public function removeToken(Request $request): JsonResponse
    {
        $user = $request->user();
        $deviceId = $request->input('device_id');

        if ($deviceId) {
            // Remove specific device
            $deleted = UserDevice::where('user_id', $user->id)
                ->where('device_id', $deviceId)
                ->delete();

            if ($deleted) {
                return response()->json([
                    'message' => 'Device token removed successfully',
                    'device_id' => $deviceId,
                ]);
            }

            return response()->json([
                'error' => 'Device not found',
                'message' => 'No device found with the provided device_id',
            ], 404);
        }

        // Remove all devices for this user
        $deleted = UserDevice::where('user_id', $user->id)->delete();


        return response()->json([
            'message' => 'All push tokens removed successfully',
            'devices_removed' => $deleted,
        ]);
    }

    /**
     * Send a test notification to the current user (all devices)
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check if user has any devices
        $deviceCount = $user->devices()->count();

        if ($deviceCount === 0) {
            return response()->json([
                'error' => 'No push token registered',
                'message' => 'Please register a push token first',
            ], 400);
        }

        // Send a test notification (will be sent to all devices)
        $user->notify(new WorkoutCompletedNotification('Test Workout'));

        return response()->json([
            'message' => 'Test notification sent successfully',
            'sent_to_devices' => $deviceCount,
        ]);
    }

    /**
     * Get current user's push token status and registered devices
     */
    public function getTokenStatus(Request $request): JsonResponse
    {
        $user = $request->user();
        $devices = $user->devices;

        return response()->json([
            'has_token' => $devices->isNotEmpty(),
            'devices' => $devices->map(fn($device) => [
                'id' => $device->id,
                'device_id' => $device->device_id,
                'device_name' => $device->device_name,
                'platform' => $device->platform,
                'last_used_at' => $device->last_used_at,
                'created_at' => $device->created_at,
            ]),
            'device_count' => $devices->count(),
        ]);
    }
}

