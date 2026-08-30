<?php

return [
    'statuses' => [
        '404' => [
            'title' => 'Seite nicht gefunden',
            'message' => 'Diese Seite gibt es nicht oder nicht mehr. Von hier kommst du weiter.',
        ],
        '403' => [
            'title' => 'Kein Zugriff',
            'message' => 'Für diese Seite fehlt dir die Berechtigung.',
        ],
        '500' => [
            'title' => 'Etwas ist schiefgelaufen',
            'message' => 'Auf unserer Seite ist ein Fehler aufgetreten. Bitte versuche es gleich noch einmal.',
        ],
        '503' => [
            'title' => 'Kurz nicht erreichbar',
            'message' => 'Wir sind gleich wieder da. Bitte versuche es in ein paar Minuten erneut.',
        ],
    ],
    'default' => [
        'title' => 'Etwas ist schiefgelaufen',
        'message' => 'Hier ist etwas nicht wie geplant. Von hier kommst du weiter.',
    ],
    'home' => 'Zur Startseite',
    'app' => 'Hol dir die fytrr App',
    'app_pitch' => 'Trainingsplan, Ernährungsplan und Tracking, angepasst von deinem KI-Coach.',
    'quicklinks' => 'Beliebte Seiten',
    'calorie' => 'Kalorienrechner',
    'calorie_desc' => 'Täglichen Kalorienbedarf berechnen',
    'workout' => 'Trainingsplan',
    'workout_desc' => 'Kostenloser Plan für dein Ziel',
];
