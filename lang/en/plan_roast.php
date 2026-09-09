<?php

return [
    'meta' => [
        'title' => 'Workout Plan Check: free, honest roast of your training plan',
        'description' => 'Paste or upload your training plan and get an honest, evidence-based verdict in seconds: volume, frequency, progression, muscle coverage and recovery.',
        'og_image_alt' => 'Workout plan check by fytrr',
    ],

    'schema' => [
        'name' => 'Workout Plan Check',
    ],

    'reviewed_date' => '2026-09-09',

    'howto' => [
        'name' => 'How to check your workout plan',
        'description' => 'Get an evidence-based verdict on your training plan in three steps.',
        'steps' => [
            ['name' => 'Add your plan', 'text' => 'Paste the text, drop a txt or PDF, or upload photos of your plan. Several days at once is fine.'],
            ['name' => 'Get your score', 'text' => 'We score muscle coverage, weekly volume, frequency, progression and recovery against the training evidence.'],
            ['name' => 'Read the verdict', 'text' => 'A coach-style verdict explains what is strong, what is weak and the one thing to fix first.'],
        ],
    ],

    'faqs' => [
        [
            'question' => 'Is the workout plan check free?',
            'answer' => 'Yes, completely free and no sign-up. Paste or upload your plan and get an instant, evidence-based score.',
        ],
        [
            'question' => 'What does it check?',
            'answer' => 'Muscle coverage, weekly volume, training frequency, progressive overload and recovery. These are the factors that decide whether a plan actually builds muscle and strength.',
        ],
        [
            'question' => 'How is the score calculated?',
            'answer' => 'Deterministically, against published strength-training evidence: roughly 10 to 20 hard sets per muscle per week, training each muscle about twice a week, and progressive overload. The same plan always gets the same score, with no random opinions.',
        ],
        [
            'question' => 'Can I upload a screenshot or PDF?',
            'answer' => 'Yes. Paste text, drop a txt or PDF, or upload photos, including several photos of a multi-day plan at once. JPG, PNG and WebP are all supported.',
        ],
        [
            'question' => 'Do you store my plan?',
            'answer' => 'We store the plan anonymously to improve the tool and to show how plans compare. There is no account and it is never shared.',
        ],
    ],

    'dimensions' => [
        'coverage' => [
            'label' => 'Muscle coverage',
            'evidence' => 'Balanced programs train every major muscle group. Skipping legs or back leads to imbalances and higher injury risk.',
        ],
        'volume' => [
            'label' => 'Weekly volume',
            'evidence' => 'Roughly 10 to 20 hard sets per muscle per week maximises hypertrophy; far less does little, far more only adds fatigue (Schoenfeld et al., 2017).',
        ],
        'balance' => [
            'label' => 'Muscle balance',
            'evidence' => 'Volume should be spread across the body. Hammering one group while another barely gets trained builds strength and physique imbalances.',
        ],
        'intensity' => [
            'label' => 'Intensity',
            'evidence' => 'Hard sets taken close to failure drive growth. Light, submaximal work is a weak stimulus no matter how many sets you do.',
        ],
        'progression' => [
            'label' => 'Progressive overload',
            'evidence' => 'Without progressively increasing weight, reps or RPE, adaptation stalls quickly (ACSM, 2009).',
        ],
        'frequency' => [
            'label' => 'Training frequency',
            'evidence' => 'Training a muscle about twice a week beats once for the same weekly volume (Schoenfeld et al., 2016).',
        ],
        'recovery' => [
            'label' => 'Recovery',
            'evidence' => 'Muscle grows during recovery; training with no rest day raises injury and burnout risk.',
        ],
    ],

    'sources' => [
        [
            'authors' => 'Schoenfeld BJ, Ogborn D, Krieger JW',
            'title' => 'Dose-response relationship between weekly resistance training volume and increases in muscle mass: a systematic review and meta-analysis',
            'publication' => 'Journal of Sports Sciences',
            'year' => '2017',
            'url' => 'https://pubmed.ncbi.nlm.nih.gov/27433992/',
        ],
        [
            'authors' => 'Schoenfeld BJ, Ogborn D, Krieger JW',
            'title' => 'Effects of resistance training frequency on measures of muscle hypertrophy: a systematic review and meta-analysis',
            'publication' => 'Sports Medicine',
            'year' => '2016',
            'url' => 'https://pubmed.ncbi.nlm.nih.gov/27102172/',
        ],
        [
            'authors' => 'American College of Sports Medicine',
            'title' => 'Progression models in resistance training for healthy adults (position stand)',
            'publication' => 'Medicine & Science in Sports & Exercise',
            'year' => '2009',
            'url' => 'https://pubmed.ncbi.nlm.nih.gov/19204579/',
        ],
    ],
];
