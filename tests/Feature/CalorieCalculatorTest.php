<?php

use App\Enums\BodyGoal;
use App\Enums\Gender;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CalorieCalculatorController;
use App\Services\CalorieCalculatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

describe('CalorieCalculatorService', function () {
    it('drives results off the product Metabolism helper', function () {
        $result = app(CalorieCalculatorService::class)->calculate(
            gender: Gender::MALE,
            age: 30,
            heightCm: 180,
            weightKg: 80,
            activity: 'moderate',
            goal: BodyGoal::LOSE_WEIGHT,
        );

        // BMR = 10*80 + 6.25*180 - 5*30 + 5 = 1780; TDEE = 1780 * 1.55 = 2759;
        // calories = 2759 - 500 (LOSE_WEIGHT adjustment) = 2259.
        expect($result['bmr'])->toBe(1780)
            ->and($result['tdee'])->toBe(2759)
            ->and($result['calories'])->toBe(2259)
            ->and($result['goalDelta'])->toBe(-500)
            ->and($result['protein']['grams'])->toBeGreaterThan(0)
            ->and($result['carbs']['grams'])->toBeGreaterThan(0)
            ->and($result['fat']['grams'])->toBeGreaterThan(0);
    });

    it('produces macro shares that sum to 1', function () {
        $result = app(CalorieCalculatorService::class)->calculate(
            gender: Gender::FEMALE,
            age: 28,
            heightCm: 168,
            weightKg: 62,
            activity: 'active',
            goal: BodyGoal::GET_FIT,
        );

        $shareSum = $result['protein']['share'] + $result['carbs']['share'] + $result['fat']['share'];
        expect($shareSum)->toBeGreaterThan(0.99)->toBeLessThan(1.01);
    });

    it('scales the TDEE by the selected activity factor', function () {
        $service = app(CalorieCalculatorService::class);

        $sedentary = $service->calculate(Gender::MALE, 30, 180, 80, 'sedentary', BodyGoal::GET_FIT);
        $veryActive = $service->calculate(Gender::MALE, 30, 180, 80, 'veryActive', BodyGoal::GET_FIT);

        expect($sedentary['tdee'])->toBe(2136) // 1780 * 1.2
            ->and($veryActive['tdee'])->toBe(3382) // 1780 * 1.9
            ->and($veryActive['tdee'])->toBeGreaterThan($sedentary['tdee']);
    });

    it('computes from the raw string reload payload', function () {
        $result = app(CalorieCalculatorService::class)->calculateFromArray([
            'gender' => 'female',
            'age' => '32',
            'height' => '170',
            'weight' => '68',
            'activity' => 'moderate',
            'goal' => 'lose',
        ]);

        expect($result)->toHaveKeys(['bmr', 'tdee', 'calories', 'goalDelta', 'protein', 'carbs', 'fat'])
            ->and($result['calories'])->toBeGreaterThan(0);
    });
});

describe('Calorie calculator page', function () {
    // The mcamara locale route group does not register under the Pest kernel
    // (all /de and /en routes 404 without cached config, which `composer test`
    // clears), so the controller is exercised directly through its Inertia
    // response — the same path a real request takes, minus routing.
    $render = function (array $data = []): array {
        app()->setLocale('de');
        $request = Request::create('/de/kostenlose-tools/kalorienrechner', 'GET', $data);
        $request->headers->set('X-Inertia', 'true');

        $inertia = app(CalorieCalculatorController::class)($request);

        return $inertia->toResponse($request)->getData(true);
    };

    it('renders the component with a null result on the initial load', function () use ($render) {
        $page = $render();

        expect($page['component'])->toBe('CalorieCalculator')
            ->and($page['props']['result'])->toBeNull()
            ->and($page['props'])->toHaveKeys(['schema', 'meta', 'author', 'internalLinks'])
            ->and($page['props']['meta']['canonical'])->toContain('/de/kostenlose-tools/kalorienrechner')
            ->and($page['props']['meta']['ogImage'])->toContain('/assets/images/og/kalorienrechner.webp');
    });

    it('computes the result from the request on a partial reload', function () use ($render) {
        $page = $render([
            'gender' => 'male',
            'age' => 30,
            'height' => 180,
            'weight' => 80,
            'activity' => 'moderate',
            'goal' => 'lose',
        ]);

        expect($page['props']['result']['calories'])->toBe(2259)
            ->and($page['props']['result']['bmr'])->toBe(1780);
    });

    it('exposes WebApplication, HowTo and FAQPage schema graphs', function () use ($render) {
        $types = array_column($render()['props']['schema'], '@type');

        expect($types)->toContain('WebApplication')
            ->toContain('HowTo')
            ->toContain('FAQPage');
    });
});

describe('Calorie guide consolidation', function () {
    it('301-redirects the German calorie guide to the calculator', function () {
        app()->setLocale('de');

        $response = app(BlogController::class)->show('kalorienbedarf-berechnen');

        expect($response)->toBeInstanceOf(RedirectResponse::class)
            ->and($response->getStatusCode())->toBe(301)
            ->and($response->headers->get('Location'))->toContain('/de/kostenlose-tools/kalorienrechner');
    });

    it('301-redirects the English calorie guide to the calculator', function () {
        app()->setLocale('en');

        $response = app(BlogController::class)->show('calorie-needs');

        expect($response)->toBeInstanceOf(RedirectResponse::class)
            ->and($response->getStatusCode())->toBe(301)
            ->and($response->headers->get('Location'))->toContain('/en/free-tools/calorie-calculator');
    });

    it('removes the consolidated guides from the blog config', function () {
        expect(config('blog.de'))->not->toHaveKey('kalorienbedarf-berechnen')
            ->and(config('blog.en'))->not->toHaveKey('calorie-needs');
    });

    it('still 404s an unknown blog slug', function () {
        app()->setLocale('de');

        expect(fn () => app(BlogController::class)->show('does-not-exist'))
            ->toThrow(NotFoundHttpException::class);
    });
});
