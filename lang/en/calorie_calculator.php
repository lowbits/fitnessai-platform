<?php

return [
    'meta' => [
        'title' => 'Calorie Calculator: Calculate Your Daily Calorie Needs',
        'description' => 'Calculate your daily calorie needs in 30 seconds. Free calorie calculator for weight loss, muscle gain or maintenance — developed by fitness experts.',
        'og_image_alt' => 'Balanced meal with chicken, rice and vegetables next to a smartphone showing a calorie app and a kitchen scale',
    ],

    'schema' => [
        'name' => 'fytrr Calorie Calculator',
    ],

    'reviewed_date' => '2026-08-30',

    'howto' => [
        'name' => 'How to calculate your calorie needs',
        'description' => 'Three steps from basal metabolic rate to a personal daily target.',
        'steps' => [
            [
                'name' => 'Calculate your basal metabolic rate',
                'text' => 'Work out your BMR with the Mifflin-St Jeor equation from gender, age, weight and height.',
            ],
            [
                'name' => 'Multiply by your activity factor',
                'text' => 'Multiply the BMR by your activity factor (1.2 sedentary to 1.9 very active). That gives your total daily energy expenditure.',
            ],
            [
                'name' => 'Adjust for your goal',
                'text' => 'Subtract about 300 to 500 kcal to lose weight, or add 200 to 400 kcal to build muscle.',
            ],
        ],
    ],

    'faqs' => [
        [
            'question' => 'How many calories should I eat per day?',
            'answer' => 'As many as your total daily expenditure sets, adjusted for your goal. For most adults that lands between 1,800 and 2,800 kcal per day. Use the calculator above for your personal number.',
        ],
        [
            'question' => 'How accurate is a calorie calculator?',
            'answer' => 'The Mifflin-St Jeor equation gives a good estimate within about 5 to 10 percent. That is enough in practice: watch your weight over two to three weeks and adjust the calories if needed.',
        ],
        [
            'question' => 'Do I have to count calories every day?',
            'answer' => 'No. It helps at the start to build a sense of portion sizes. With fytrr you photograph your food and the AI does the counting — that removes the manual effort.',
        ],
        [
            'question' => 'What is the difference between BMR and TDEE?',
            'answer' => 'BMR (Basal Metabolic Rate) is the energy your body uses at complete rest. TDEE (Total Daily Energy Expenditure) is your BMR plus all movement in daily life and exercise. TDEE is the number that matters for planning your nutrition.',
        ],
    ],
];
