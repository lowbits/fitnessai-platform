<?php

use App\Ai\Agents\PlanExtractionAgent;
use App\Ai\Agents\PlanVerdictAgent;
use App\Http\Controllers\PlanRoastController;
use App\Models\PlanEvaluation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * @return array<string, mixed>
 */
function balancedWorkoutFacts(): array
{
    $dayA = ['label' => 'A', 'groups' => [
        ['group' => 'legs', 'sets' => 7],
        ['group' => 'back', 'sets' => 7],
        ['group' => 'chest', 'sets' => 7],
    ]];
    $dayB = ['label' => 'B', 'groups' => [
        ['group' => 'shoulders', 'sets' => 7],
        ['group' => 'arms', 'sets' => 7],
        ['group' => 'core', 'sets' => 7],
    ]];

    return [
        'plan_type' => 'workout',
        'split' => 'upper/lower',
        'has_progression' => true,
        'intensity' => 'hard',
        'days' => [$dayA, $dayB, $dayA, $dayB],
    ];
}

function pastePlan(): string
{
    return str_repeat('Monday: bench press 4x8, rows 4x8. ', 3);
}

it('scores a pasted workout plan and returns a stream token', function () {
    PlanExtractionAgent::fake([balancedWorkoutFacts()]);

    $this->postJson('/api/plan-roast/evaluate', ['plan' => pastePlan(), 'tone' => 'roast', 'locale' => 'en'])
        ->assertOk()
        ->assertJsonPath('plan_type', 'workout')
        ->assertJsonPath('score', 100)
        ->assertJsonStructure(['score', 'dimensions' => [['key', 'rating', 'label', 'evidence']], 'stream_token']);
});

it('stores the evaluation with its score', function () {
    PlanExtractionAgent::fake([balancedWorkoutFacts()]);

    $this->postJson('/api/plan-roast/evaluate', ['plan' => pastePlan(), 'locale' => 'en'])
        ->assertOk();

    expect(PlanEvaluation::where('score', 100)->where('source', 'paste')->exists())->toBeTrue();
});

it('reports how the plan ranks against stored plans', function () {
    PlanEvaluation::factory()->count(4)->create(['score' => 40]);
    PlanExtractionAgent::fake([balancedWorkoutFacts()]);

    $this->postJson('/api/plan-roast/evaluate', ['plan' => pastePlan(), 'locale' => 'en'])
        ->assertJsonPath('ranking.percentile', 80)
        ->assertJsonPath('ranking.sample_size', 5);
});

it('rejects a plan that is too short', function () {
    $this->postJson('/api/plan-roast/evaluate', ['plan' => 'hi'])
        ->assertJsonValidationErrors(['plan']);
});

it('requires either a plan or a file', function () {
    $this->postJson('/api/plan-roast/evaluate', [])
        ->assertJsonValidationErrors(['plan']);
});

it('evaluates a plan from an uploaded txt file', function () {
    PlanExtractionAgent::fake([balancedWorkoutFacts()]);

    $file = UploadedFile::fake()->createWithContent('plan.txt', pastePlan());

    $this->post('/api/plan-roast/evaluate', ['files' => [$file]])
        ->assertOk()
        ->assertJsonPath('plan_type', 'workout');

    PlanExtractionAgent::assertPrompted(fn ($prompt) => str_contains($prompt->prompt, 'bench press'));
});

it('extracts and evaluates a plan from an uploaded pdf', function () {
    PlanExtractionAgent::fake([balancedWorkoutFacts()]);

    $path = tempnam(sys_get_temp_dir(), 'plan').'.pdf';
    file_put_contents($path, Pdf::loadHTML('<p>Monday: bench press 4x8, barbell rows 4x8.</p>')->output());
    $file = new UploadedFile($path, 'plan.pdf', 'application/pdf', null, true);

    $this->post('/api/plan-roast/evaluate', ['files' => [$file]])
        ->assertOk()
        ->assertJsonPath('plan_type', 'workout');

    PlanExtractionAgent::assertPrompted(fn ($prompt) => str_contains($prompt->prompt, 'bench press'));
});

it('passes the typed note as context to image extraction', function () {
    PlanExtractionAgent::fake([balancedWorkoutFacts()]);

    $this->post('/api/plan-roast/evaluate', [
        'plan' => 'I only train twice a week now, a front and back split.',
        'files' => [UploadedFile::fake()->image('day1.jpg')],
        'locale' => 'en',
    ])->assertOk()->assertJsonPath('plan_type', 'workout');

    PlanExtractionAgent::assertPrompted(fn ($prompt) => str_contains($prompt->prompt, 'twice a week')
        && $prompt->attachments->count() === 1);
});

it('treats several photos as one plan in a single extraction', function () {
    PlanExtractionAgent::fake([balancedWorkoutFacts()]);

    $files = [
        UploadedFile::fake()->image('day1.jpg'),
        UploadedFile::fake()->image('day2.png'),
        UploadedFile::fake()->image('day3.jpg'),
    ];

    $this->post('/api/plan-roast/evaluate', ['files' => $files])
        ->assertOk()
        ->assertJsonPath('plan_type', 'workout');

    PlanExtractionAgent::assertPrompted(fn ($prompt) => $prompt->attachments->count() === 3);
});

it('returns only the plan type for a non-workout plan', function () {
    PlanExtractionAgent::fake([['plan_type' => 'nutrition']]);

    $this->postJson('/api/plan-roast/evaluate', ['plan' => str_repeat('oatmeal, chicken, rice. ', 3)])
        ->assertOk()
        ->assertJsonPath('plan_type', 'nutrition')
        ->assertJsonMissing(['stream_token']);
});

it('streams a verdict for a valid token', function () {
    PlanExtractionAgent::fake([balancedWorkoutFacts()]);
    PlanVerdictAgent::fake(['This plan is genuinely solid.']);

    $token = $this->postJson('/api/plan-roast/evaluate', ['plan' => pastePlan(), 'locale' => 'en'])
        ->json('stream_token');

    $this->get("/api/plan-roast/stream/{$token}")->assertOk();
});

it('returns 404 for an unknown stream token', function () {
    $this->get('/api/plan-roast/stream/does-not-exist')->assertNotFound();
});

it('renders the tool page with meta and schema', function () {
    app()->setLocale('de');
    $request = Request::create('/de/kostenlose-tools/trainingsplan-check', 'GET');
    $request->headers->set('X-Inertia', 'true');

    $page = app(PlanRoastController::class)->index()->toResponse($request)->getData(true);

    expect($page['component'])->toBe('FreeTools/PlanRoast')
        ->and($page['props'])->toHaveKeys(['meta', 'schema'])
        ->and($page['props']['meta']['canonical'])->toContain('/de/kostenlose-tools/trainingsplan-check');
});
