<?php

use App\Models\BodyProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('user can track body progress with only weight', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/body-progress', [
            'weight' => 75.50,
        ]);


    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'user_id',
                'weight',
                'body_fat_percentage',
                'muscle_mass',
                'waist_circumference',
                'chest_circumference',
                'hip_circumference',
                'arm_circumference',
                'thigh_circumference',
                'notes',
                'recorded_at',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJson([
            'data' => [
                'weight' => '75.50',
            ],
        ]);

    $this->assertDatabaseHas('body_progress', [
        'user_id' => $this->user->id,
        'weight_kg' => 75.50,
    ]);
});

test('user can track body progress with all fields', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/body-progress', [
            'weight' => 80.00,
            'body_fat_percentage' => 18.5,
            'muscle_mass' => 65.0,
            'waist_circumference' => 85.0,
            'chest_circumference' => 100.0,
            'hip_circumference' => 95.0,
            'arm_circumference' => 35.0,
            'thigh_circumference' => 55.0,
            'notes' => 'Feeling strong today',
            'recorded_at' => now()->subDay()->toDateTimeString(),
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'weight' => '80.00',
                'body_fat_percentage' => '18.50',
                'muscle_mass' => '65.00',
                'waist_circumference' => '85.00',
                'chest_circumference' => '100.00',
                'hip_circumference' => '95.00',
                'arm_circumference' => '35.00',
                'thigh_circumference' => '55.00',
                'notes' => 'Feeling strong today',
            ],
        ]);

    $this->assertDatabaseHas('body_progress', [
        'user_id' => $this->user->id,
        'weight_kg' => 80.00,
        'body_fat_percentage' => 18.5,
        'notes' => 'Feeling strong today',
    ]);
});

test('weight is required when creating body progress', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/body-progress', [
            'body_fat_percentage' => 20.0,
        ]);


    $response->assertStatus(422)
        ->assertJsonValidationErrorFor('weight');
});

test('weight must be within valid range', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/body-progress', [
            'weight' => 10, // Too low
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['weight']);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/body-progress', [
            'weight' => 600, // Too high
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['weight']);
});

test('body fat percentage must be within valid range', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/body-progress', [
            'weight' => 75,
            'body_fat_percentage' => 150, // Invalid
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['body_fat_percentage']);
});

test('user can update body progress', function () {
    $bodyProgress = BodyProgress::factory()->create([
        'user_id' => $this->user->id,
        'weight_kg' => 75.0,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->putJson("/api/v2/track/body-progress/{$bodyProgress->id}", [
            'weight' => 76.5,
            'notes' => 'Updated weight',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'weight' => '76.50',
                'notes' => 'Updated weight',
            ],
        ]);

    $this->assertDatabaseHas('body_progress', [
        'id' => $bodyProgress->id,
        'weight_kg' => 76.5,
        'notes' => 'Updated weight',
    ]);
});

test('user can only update their own body progress', function () {
    $otherUser = User::factory()->create();
    $bodyProgress = BodyProgress::factory()->create([
        'user_id' => $otherUser->id,
        'weight_kg' => 75.0,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->putJson("/api/v2/track/body-progress/{$bodyProgress->id}", [
            'weight' => 80.0,
        ]);

    $response->assertStatus(404);
});

test('user can get their body progress history', function () {
    BodyProgress::factory()->count(5)->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/v2/track/body-progress');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'weight',
                    'recorded_at',
                ],
            ],
            'count',
        ])
        ->assertJsonCount(5, 'data');
});

test('user can get filtered body progress history', function () {
    // Create progress entries at different dates
    BodyProgress::factory()->create([
        'user_id' => $this->user->id,
        'recorded_at' => now()->subDays(10),
    ]);
    BodyProgress::factory()->create([
        'user_id' => $this->user->id,
        'recorded_at' => now()->subDays(5),
    ]);
    BodyProgress::factory()->create([
        'user_id' => $this->user->id,
        'recorded_at' => now(),
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/v2/track/body-progress?from=' . now()->subDays(7)->toDateString());

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('user can limit body progress results', function () {
    BodyProgress::factory()->count(10)->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/v2/track/body-progress?limit=3');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('user can get latest body progress', function () {
    BodyProgress::factory()->create([
        'user_id' => $this->user->id,
        'weight_kg' => 75.0,
        'recorded_at' => now()->subDays(5),
    ]);

    $latest = BodyProgress::factory()->create([
        'user_id' => $this->user->id,
        'weight_kg' => 76.5,
        'recorded_at' => now(),
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/v2/track/body-progress/latest');

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $latest->id,
                'weight' => '76.50',
            ],
        ]);
});

test('user can delete body progress', function () {
    $bodyProgress = BodyProgress::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->deleteJson("/api/v2/track/body-progress/{$bodyProgress->id}");

    $response->assertStatus(200);

    $this->assertDatabaseMissing('body_progress', [
        'id' => $bodyProgress->id,
    ]);
});

test('user can only delete their own body progress', function () {
    $otherUser = User::factory()->create();
    $bodyProgress = BodyProgress::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->deleteJson("/api/v2/track/body-progress/{$bodyProgress->id}");

    $response->assertStatus(404);

    $this->assertDatabaseHas('body_progress', [
        'id' => $bodyProgress->id,
    ]);
});

test('body progress entries are deleted when user is deleted', function () {
    $bodyProgress = BodyProgress::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->user->delete();

    $this->assertDatabaseMissing('body_progress', [
        'id' => $bodyProgress->id,
    ]);
});

test('body progress is ordered by recorded_at descending', function () {
    $oldest = BodyProgress::factory()->create([
        'user_id' => $this->user->id,
        'weight_kg' => 80.0,
        'recorded_at' => now()->subDays(10),
    ]);

    $newest = BodyProgress::factory()->create([
        'user_id' => $this->user->id,
        'weight_kg' => 75.0,
        'recorded_at' => now(),
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/v2/track/body-progress');

    $response->assertStatus(200);

    $data = $response->json('data');
    expect($data[0]['id'])->toBe($newest->id);
    expect($data[1]['id'])->toBe($oldest->id);
});

