<?php

use App\Models\User;
use App\Notifications\SetPasswordNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;


test('set password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->withoutPassword()->create();

    $this->post(route('set-password.request'),
        ['email' => $user->email])->assertOk();

    Notification::assertSentTo($user, SetPasswordNotification::class);
});
