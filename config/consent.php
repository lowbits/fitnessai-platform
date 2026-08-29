<?php

$de = [
    'disclosure_trigger' => 'Welche Daten genau?',
    'disclosure_items' => [
        'Ziel und Zielgewicht',
        'Alter, Größe, Gewicht',
        'Ernährungsweise, Allergien, Abneigungen',
        'Trainingslevel und Equipment',
    ],
    'disclosure_footnote' => 'Die Angaben gehen ohne Namen, E-Mail oder Account-ID raus. Zu deinem Profil zusammengeführt werden sie nur in fytrr.',
    'privacy_link' => 'Datenschutzerklärung',
    'primary' => 'Einverstanden',
    'secondary' => 'Nicht einverstanden',
];

$en = [
    'disclosure_trigger' => 'Which data exactly?',
    'disclosure_items' => [
        'Goal and target weight',
        'Age, height, weight',
        'Diet, allergies, dislikes',
        'Training level and equipment',
    ],
    'disclosure_footnote' => 'Your details are sent without your name, email, or account ID. They are only matched to your profile inside fytrr.',
    'privacy_link' => 'Privacy Policy',
    'primary' => 'I agree',
    'secondary' => "Don't agree",
];

return [
    'current_version' => env('CONSENT_VERSION', '2026-08-27'),

    'enforce' => env('CONSENT_ENFORCE', false),

    // TODO(consent-rollout): temporary. The first app release that collects consent.
    // Older clients (no consent screen) keep generating the plan at signup so they
    // are not left plan-less; consent-capable clients defer generation to the grant.
    // Remove this gate once every client is >= this version.
    'min_app_version' => env('CONSENT_MIN_APP_VERSION', '2.2.0'),

    'providers' => ['OpenAI', 'Mistral AI'],

    'copy' => [
        'de' => [
            'onboarding' => [
                'title' => 'Bevor dein Plan entsteht',
                'body' => 'Für deinen Plan sendet fytrr deine Angaben an OpenAI und Mistral AI. Name und E-Mail bleiben bei uns.',
                ...$de,
            ],
            'sheet' => [
                'title' => 'Deine Pläne, deine Daten',
                'body' => 'Für deine Pläne sendet fytrr deine Trainings- und Ernährungsangaben an OpenAI und Mistral AI. Name und E-Mail bleiben bei uns.',
                ...$de,
            ],
            'settings' => [
                'title' => 'KI & Daten',
                'row_plan' => 'Plan-Erstellung mit KI',
                'status_active' => 'Aktiv',
                'status_off' => 'Aus',
                'sent_title_active' => 'Was gesendet wird',
                'sent_title_revoked' => 'Was gesendet würde',
                'sent_body' => 'Dein Ziel, Alter, Größe, Gewicht sowie Ernährungs- und Trainingsangaben gehen an OpenAI und Mistral AI. Name und E-Mail bleiben bei uns.',
                'privacy_link' => 'Datenschutzerklärung',
                'action_revoke' => 'Einwilligung widerrufen',
                'action_grant' => 'Einwilligung erteilen',
                'footer_active' => 'Erteilt am {date}. Widerruf stoppt neue Pläne und Mona. Bestehende Pläne und dein Tracking bleiben.',
                'footer_revoked' => 'Ohne Einwilligung erstellt fytrr keine neuen Pläne und Mona ist pausiert. Bestehende Pläne und dein Tracking bleiben.',
            ],
        ],
        'en' => [
            'onboarding' => [
                'title' => 'Before your plan is created',
                'body' => 'To create your plan, fytrr sends your details to OpenAI and Mistral AI. Your name and email stay with us.',
                ...$en,
            ],
            'sheet' => [
                'title' => 'Your plans, your data',
                'body' => 'To create your plans, fytrr sends your training and nutrition details to OpenAI and Mistral AI. Your name and email stay with us.',
                ...$en,
            ],
            'settings' => [
                'title' => 'AI & Data',
                'row_plan' => 'AI plan creation',
                'status_active' => 'Active',
                'status_off' => 'Off',
                'sent_title_active' => 'What is sent',
                'sent_title_revoked' => 'What would be sent',
                'sent_body' => 'Your goal, age, height, weight, and your nutrition and training details are sent to OpenAI and Mistral AI. Your name and email stay with us.',
                'privacy_link' => 'Privacy Policy',
                'action_revoke' => 'Withdraw consent',
                'action_grant' => 'Give consent',
                'footer_active' => 'Granted on {date}. Withdrawing stops new plans and Mona. Existing plans and your tracking remain.',
                'footer_revoked' => 'Without consent, fytrr does not create new plans and Mona is paused. Existing plans and your tracking remain.',
            ],
        ],
    ],
];
