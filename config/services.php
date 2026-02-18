<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://0.0.0.0:7700'),
        'key' => env('MEILISEARCH_API_KEY'),
    ],
    'openfoodfacts' => [
        'db_dump_url' => env('OPENFOODFACTS_URL', 'https://static.openfoodfacts.org/data/en.openfoodfacts.org.products.json.gz'),
    ],

    'rembg' => [
        'url' => env('REMBG_URL', 'https://rembg.lowbits.de'),
    ],
    'photoroom' => [
        'api_key' => 'sandbox_sk_pr_default_4c785cb9271319d33798336ce0e1e1c31e75399a',
    ],

];
