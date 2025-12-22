<?php

use App\Http\Controllers\Api\V2\OnboardingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v2')->group(function () {
    Route::post('/onboarding', [OnboardingController::class, 'store']);
});
