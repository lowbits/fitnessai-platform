<?php

namespace Tests\Feature;

use App\Enums\BodyGoal;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnumLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_body_goal_label_respects_app_locale()
    {
        // Set app locale to German
        app()->setLocale('de');

        /** @var User $user */
        $user = User::factory()->create(['locale' => 'de']);
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'body_goal' => BodyGoal::MUSCLE_GAIN,
        ]);

        // When app locale is 'de', label() should return German
        $this->assertEquals('Muskeln aufbauen', $user->profile->body_goal->label());
        $this->assertEquals('Baue Muskelmasse auf und steigere deine Kraft', $user->profile->body_goal->description());
    }

    public function test_body_goal_label_accepts_explicit_locale()
    {
        app()->setLocale('en');

        $bodyGoal = BodyGoal::MUSCLE_GAIN;

        $this->assertEquals('Build Muscle', $bodyGoal->label('en'));
        $this->assertEquals('Muskeln aufbauen', $bodyGoal->label('de'));
    }

    public function test_auth_me_endpoint_returns_labels_in_user_preferred_locale()
    {
        app()->setLocale('en');

        /** @var User $user */
        $user = User::factory()->create(['locale' => 'de']);
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'body_goal' => BodyGoal::MUSCLE_GAIN,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v2/auth/me');

        $response->assertStatus(200);
        $response->assertJsonPath('user.profile.body_goal', 'Muskeln aufbauen');
    }
}
