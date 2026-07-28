<?php

return [
    'meta' => [
        'title' => 'Free Macro Calculator: Calculate Your Daily Macros | fytrr',
        'description' => 'Calculate your daily protein, carbs, and fat targets for free. Based on your goals, body stats, and activity level. Get a meal plan that fits your macros.',
    ],

    'schema' => [
        'name' => 'fytrr Macro Calculator',
    ],

    'reviewed_date' => '2026-07-28',

    'faqs' => [
        [
            'question' => 'What are macronutrients?',
            'answer' => 'Macronutrients — or "macros" — are the three nutrients that supply energy: protein, carbohydrates and fat. Protein and carbs provide about 4 kcal per gram, fat about 9 kcal per gram. Hitting the right balance of macros, not just the right calorie total, is what shapes body composition: enough protein preserves and builds muscle, while carbs and fat cover energy and hormonal health.',
        ],
        [
            'question' => 'How does the macro calculator work?',
            'answer' => 'We estimate your basal metabolic rate with the Mifflin-St Jeor equation, then add energy for your daily activity level and your weekly training sessions to get your total daily energy expenditure. We adjust for your goal (a deficit to lose weight, a surplus to build muscle), set protein based on your goal and bodyweight, protect a minimum fat intake for hormonal health, and fill the remaining calories with carbohydrates and fat according to your diet.',
        ],
        [
            'question' => 'How much protein do I need?',
            'answer' => 'It depends on your goal. For weight loss we target roughly 2.5 g per kg of lean body mass, because higher protein preserves muscle in a calorie deficit and keeps you full. For muscle gain we use about 2.0 g per kg of bodyweight, and for maintenance around 1.8 g per kg. Protein is capped at 35% of your daily calories as a practical upper limit.',
        ],
        [
            'question' => 'How are my carbs and fat calculated?',
            'answer' => 'After protein is set, we reserve a minimum of 0.8 g of fat per kg of bodyweight to protect hormonal health — this matters especially for women in a deficit. The calories that remain are split between carbohydrates and fat based on your diet: a balanced omnivore split leans slightly toward carbs, while a plant-based diet shifts a little further toward them. You can fine-tune the exact split later in the fytrr app.',
        ],
        [
            'question' => 'Do these macros match the fytrr app?',
            'answer' => 'Yes. This calculator runs the exact same formulas the fytrr app uses to build your personalised nutrition plan. The numbers you see here are the numbers the app works from — the app simply turns them into concrete meals, recipes and a shopping list that add up to your targets.',
        ],
        [
            'question' => 'Is this a substitute for medical or nutritional advice?',
            'answer' => 'No. The results are science-based estimates for healthy adults and are intended as a practical starting point, not individual medical or nutritional advice. If you are pregnant, have a medical condition or specific dietary needs, please consult a doctor or registered dietitian.',
        ],
    ],
];
