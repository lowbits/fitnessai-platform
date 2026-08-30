<?php

return [
    'statuses' => [
        '404' => [
            'title' => 'Page not found',
            'message' => 'This page does not exist, or no longer does. Here is the way forward.',
        ],
        '403' => [
            'title' => 'No access',
            'message' => 'You do not have permission to view this page.',
        ],
        '500' => [
            'title' => 'Something went wrong',
            'message' => 'An error occurred on our side. Please try again in a moment.',
        ],
        '503' => [
            'title' => 'Briefly unavailable',
            'message' => 'We will be back shortly. Please try again in a few minutes.',
        ],
    ],
    'default' => [
        'title' => 'Something went wrong',
        'message' => 'Something here did not go as planned. Here is the way forward.',
    ],
    'home' => 'Back to home',
    'app' => 'Get the fytrr app',
    'app_pitch' => 'Workout plans, meal plans and tracking, tuned by your AI coach.',
    'quicklinks' => 'Popular pages',
    'calorie' => 'Calorie calculator',
    'calorie_desc' => 'Work out your daily calorie needs',
    'workout' => 'Workout plan',
    'workout_desc' => 'A free plan for your goal',
];
