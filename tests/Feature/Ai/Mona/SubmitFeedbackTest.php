<?php

use App\Ai\Tools\SubmitFeedbackTool;
use App\Models\AppFeedback;
use App\Models\User;
use Laravel\Ai\Tools\Request;

function submitFeedback(User $user, array $args): array
{
    return json_decode((new SubmitFeedbackTool($user))->handle(new Request($args)), true);
}

test('it stores feedback and confirms with a widget', function () {
    $user = User::factory()->create(['locale' => 'de']);

    $result = submitFeedback($user, ['type' => 'feature_request', 'message' => 'Ich will meinen Trainingsplan umplanen können.']);

    expect($result['widget'])->toBe('feedback_received');
    expect($result['data']['type'])->toBe('feature_request');

    $row = AppFeedback::sole();
    expect($row->user_id)->toBe($user->id);
    expect($row->message)->toBe('Ich will meinen Trainingsplan umplanen können.');
    expect($row->context)->toBe(['locale' => 'de']);
    expect($row->status)->toBe('new');
});

test('an unknown type falls back to feature_request', function () {
    $user = User::factory()->create();

    submitFeedback($user, ['type' => 'nonsense', 'message' => 'more dark mode please']);

    expect(AppFeedback::sole()->type)->toBe('feature_request');
});

test('empty feedback is not stored', function () {
    $user = User::factory()->create();

    expect(submitFeedback($user, ['message' => '   '])['error'])->toBe('empty_feedback');
    expect(AppFeedback::count())->toBe(0);
});
