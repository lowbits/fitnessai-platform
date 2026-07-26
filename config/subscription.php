<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mobile Free Trial
    |--------------------------------------------------------------------------
    |
    | Length of the free trial a mobile user can start from the paywall, and how
    | many days at the start of the plan are unlocked without a subscription
    | (0 = everything behind the paywall, 1 = the first day is a free preview).
    |
    */
    'trial_days' => (int) env('TRIAL_DAYS', 7),
    'preview_days' => (int) env('PREVIEW_DAYS', 0),

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
