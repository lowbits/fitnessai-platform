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
        'db_dump_url' => env('OPENFOODFACTS_URL', 'https://static.openfoodfacts.org/data/openfoodfacts-products.jsonl.gz'),
    ],

    'r2' => [
        'public_url' => env('R2_PUBLIC_URL', 'https://pub-3f5447ea608c4617b61c972ef190b448.r2.dev'),
    ],

    'rembg' => [
        'url' => env('REMBG_URL', 'https://rembg.lowbits.de'),
    ],
    'photoroom' => [
        'api_key' => env('PHOTOROOOM_API_KEY', 'sandbox_sk_pr_default_4c785cb9271319d33798336ce0e1e1c31e75399a'),
    ],

    'indexnow' => [
        // Public by design — this key is served at public/<key>.txt for domain verification.
        'key' => env('INDEXNOW_KEY', 'cadcb149cb1d40cead98f92e71c0c536'),
        'endpoint' => env('INDEXNOW_ENDPOINT', 'https://api.indexnow.org/indexnow'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect' => env('APPLE_REDIRECT_URI'),
    ],

];
