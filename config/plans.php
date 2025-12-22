<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Plan Duration
    |--------------------------------------------------------------------------
    |
    | The total number of days for a complete fitness plan.
    | This affects meal plan and workout plan generation.
    |
    */

    'duration_days' => env('PLAN_DURATION_DAYS', 28),

    /*
    |--------------------------------------------------------------------------
    | Meals Per Day
    |--------------------------------------------------------------------------
    |
    | The number of meals to generate per day.
    |
    */

    'meals_per_day' => env('PLAN_MEALS_PER_DAY', 4),

    /*
    |--------------------------------------------------------------------------
    | Generation Settings
    |--------------------------------------------------------------------------
    |
    | Settings for plan generation process.
    |
    */

    'generation' => [
        // How many days to generate in development mode
        'dev_duration_days' => env('PLAN_DEV_DURATION_DAYS', 3),

        // Use reduced duration in development
        'use_dev_mode' => env('PLAN_USE_DEV_MODE', false),
    ],

];

