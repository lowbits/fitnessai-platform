<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Activity credit
    |--------------------------------------------------------------------------
    |
    | How much of the active energy a user burns is credited back to their
    | daily calorie budget. Conservative on purpose: only a fraction counts,
    | and the daily credit is capped. Tunable here so the policy can change
    | without shipping a new app build.
    |
    */

    'credit_factor' => (float) env('HEALTH_CREDIT_FACTOR', 0.5),

    'credit_cap_kcal' => (int) env('HEALTH_CREDIT_CAP_KCAL', 500),
];
