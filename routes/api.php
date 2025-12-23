<?php

use App\Http\Controllers\Api\V2\OnboardingController;
use Illuminate\Support\Facades\Route;


Route::prefix('v2')->group(function () {
    Route::post('/onboarding', [OnboardingController::class, 'store'])
        ->middleware('throttle:3,1'); // 3 attempts per minute
});
