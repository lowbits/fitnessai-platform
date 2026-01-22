<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Database\Eloquent\Factories\Factory;
use NotificationChannels\Expo\ExpoPushToken;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDevice>
 */
class UserDeviceFactory extends Factory
{
    protected $model = UserDevice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'device_id' => fake()->uuid(),
            'expo_push_token' => ExpoPushToken::make('ExponentPushToken[' . fake()->regexify('[a-zA-Z0-9_-]{22}') . ']'),
            'device_name' => fake()->randomElement([
                'iPhone 14 Pro',
                'Samsung Galaxy S23',
                'Google Pixel 7',
                'iPhone 13',
                'OnePlus 11',
            ]),
            'platform' => fake()->randomElement(['ios', 'android']),
            'last_used_at' => now(),
        ];
    }
}

