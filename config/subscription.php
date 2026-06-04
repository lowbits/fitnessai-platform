<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mobile Free Trial
    |--------------------------------------------------------------------------
    |
    | Number of days a mobile user gets free access before the paywall.
    | This controls trial_ends_at and the initial plan duration for mobile.
    |
    */
    'trial_days' => (int) env('TRIAL_DAYS', 3),

    /*
    |--------------------------------------------------------------------------
    | Web PDF Plan
    |--------------------------------------------------------------------------
    |
    | Number of days to generate for the free web PDF plan.
    | Web users receive a one-time PDF — no ongoing trial/paywall.
    |
    */
    'pdf_plan_days' => (int) env('PDF_PLAN_DAYS', 7),

];
