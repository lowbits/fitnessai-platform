<?php

return [
    'meta' => [
        'title' => 'Fitness Glossary: Training and Nutrition Terms Explained Simply',
        'description' => 'RPE, tempo, TDEE, progressive overload and more. Every term from your training and nutrition plan explained clearly, with examples.',
        'og_image_alt' => 'fytrr fitness glossary',
    ],

    'hero' => [
        'eyebrow' => 'Fitness glossary',
        'h1' => 'Training terms, explained simply',
        'subtitle' => 'RPE, tempo, TDEE, volume. Every term that shows up in your plan, explained in one sentence, with an example.',
    ],

    'ui' => [
        'search_placeholder' => 'Search a term, e.g. RPE or deficit',
        'no_results' => 'No term found. Try a different word.',
        'all' => 'All',
        'example' => 'Example',
        'related_heading' => 'Related free tools',
    ],

    'categories' => [
        'training' => 'Training & programming',
        'muscles' => 'Muscle groups',
        'nutrition' => 'Nutrition',
        'tracking' => 'Tracking & progress',
    ],

    'terms' => [
        'rpe' => [
            'term' => 'RPE',
            'expansion' => 'Rate of Perceived Exertion',
            'category' => 'training',
            'definition' => 'How hard a set was, on a 1 to 10 scale, measured by how many more reps you could have done.',
            'example' => 'RPE 8 means about 2 reps were left in the tank.',
        ],
        'rir' => [
            'term' => 'RIR',
            'expansion' => 'Reps in Reserve',
            'category' => 'training',
            'definition' => 'The number of reps you have left at the end of a set. The flip side of RPE: 2 RIR equals RPE 8.',
            'example' => '2 RIR means you could have done 2 more clean reps.',
        ],
        'tempo' => [
            'term' => 'Tempo',
            'expansion' => 'e.g. 3-0-1-0',
            'category' => 'training',
            'definition' => 'Four numbers for the seconds of one rep: lowering (eccentric), pause at the bottom, lifting (concentric), pause at the top.',
            'example' => '3-0-1-0 means lower for 3 seconds, no pause, lift for 1, no pause.',
        ],
        'time-under-tension' => [
            'term' => 'Time under tension',
            'expansion' => '',
            'category' => 'training',
            'definition' => 'How long a muscle stays under load during a set. Lowering slowly increases it and can add to the growth stimulus.',
            'example' => 'A slow 3-second lowering phase noticeably raises time under tension.',
        ],
        'one-rm' => [
            'term' => '1RM',
            'expansion' => 'One-Rep Max',
            'category' => 'training',
            'definition' => 'The most weight you can lift once with good form on an exercise. Training loads are often given as a percentage of it.',
            'example' => '80% of 1RM is 80 kg when your 1RM is 100 kg.',
        ],
        'progressive-overload' => [
            'term' => 'Progressive overload',
            'expansion' => '',
            'category' => 'training',
            'definition' => 'The core principle behind progress: gradually increase the training stimulus over time, through more weight, more reps or more sets.',
            'example' => 'Once you hit the top of the rep range on every set, add weight next week.',
        ],
        'volume' => [
            'term' => 'Training volume',
            'expansion' => 'hard sets per week',
            'category' => 'training',
            'definition' => 'The amount of hard working sets a muscle gets per week. The single biggest lever for muscle growth.',
            'example' => 'Around 10 to 20 hard sets per muscle per week is considered productive for growth.',
        ],
        'frequency' => [
            'term' => 'Training frequency',
            'expansion' => '',
            'category' => 'training',
            'definition' => 'How often you train a muscle per week. At the same volume, twice a week usually beats once.',
            'example' => 'Training chest Monday and Thursday means a frequency of 2 for chest.',
        ],
        'compound-isolation' => [
            'term' => 'Compound vs. isolation',
            'expansion' => '',
            'category' => 'training',
            'definition' => 'Compound lifts move several joints and muscles at once (squat, bench press). Isolation exercises target a single muscle (biceps curl).',
            'example' => 'The squat is a compound lift, the leg extension is an isolation exercise.',
        ],
        'set-rep' => [
            'term' => 'Set & rep',
            'expansion' => '',
            'category' => 'training',
            'definition' => 'A rep is one complete movement, a set is a group of reps done back to back, followed by a rest.',
            'example' => '3 sets of 10 reps means ten reps, three times, with rest in between.',
        ],
        'superset' => [
            'term' => 'Superset',
            'expansion' => '',
            'category' => 'training',
            'definition' => 'Two exercises done back to back with no rest between them, often for opposing muscles, to save time.',
            'example' => 'A biceps curl straight into a triceps press is a superset.',
        ],
        'drop-set' => [
            'term' => 'Drop set',
            'expansion' => '',
            'category' => 'training',
            'definition' => 'After your last set, immediately reduce the weight and keep going with no rest, for extra stimulus to exhaustion.',
            'example' => 'After 10 heavy reps, drop 30% and continue straight to failure.',
        ],
        'training-to-failure' => [
            'term' => 'Training to failure',
            'expansion' => 'or close to it',
            'category' => 'training',
            'definition' => 'A set taken near the point where no clean rep is possible. Close to failure (not always all the way) is a strong growth stimulus.',
            'example' => 'At RPE 9 to 10 you are training close to or all the way to failure.',
        ],
        'hypertrophy' => [
            'term' => 'Hypertrophy',
            'expansion' => 'muscle growth',
            'category' => 'training',
            'definition' => 'The growth of muscle fibers through training. The goal when you want bigger, stronger muscles.',
            'example' => 'Hypertrophy training usually uses 8 to 12 reps and moderate rest.',
        ],
        'hiit' => [
            'term' => 'HIIT',
            'expansion' => 'High-Intensity Interval Training',
            'category' => 'training',
            'definition' => 'Interval training that alternates short, very hard efforts with short recoveries. Efficient for calorie burn and conditioning.',
            'example' => '30 seconds sprint, 30 seconds walk, repeated several times.',
        ],
        'split' => [
            'term' => 'Training split',
            'expansion' => 'e.g. Push/Pull/Legs, Upper/Lower, Full Body',
            'category' => 'training',
            'definition' => 'How you divide your muscle groups across training days. The right choice depends mostly on how often you train per week.',
            'example' => 'At 3 days, Full Body hits each muscle more often than Push/Pull/Legs.',
        ],
        'deload' => [
            'term' => 'Deload',
            'expansion' => '',
            'category' => 'training',
            'definition' => 'A deliberately lighter week with less volume or intensity, so your body and nervous system recover and you come back stronger.',
            'example' => 'After several hard weeks, a deload week at half the volume can help.',
        ],
        'mobility' => [
            'term' => 'Mobility',
            'expansion' => '',
            'category' => 'training',
            'definition' => 'The active range of motion of a joint. Better mobility improves technique and helps prevent injury.',
            'example' => 'Hip mobility drills before squatting improve your depth.',
        ],

        'lats' => [
            'term' => 'Lats',
            'expansion' => 'Latissimus dorsi',
            'category' => 'muscles',
            'definition' => 'The large, wide back muscle along your sides. It creates the V-taper and drives pull-ups and rows.',
            'example' => 'Pull-ups and rows mainly work the lats.',
        ],
        'rear-delts' => [
            'term' => 'Rear delts',
            'expansion' => 'rear deltoids',
            'category' => 'muscles',
            'definition' => 'The back portion of the shoulder muscle. Often underdeveloped, important for healthy shoulders and posture.',
            'example' => 'Reverse flys target the rear delts directly.',
        ],
        'posterior-chain' => [
            'term' => 'Posterior chain',
            'expansion' => '',
            'category' => 'muscles',
            'definition' => 'All the muscles along the back of your body: calves, hamstrings, glutes and spinal erectors. Central to strength and posture.',
            'example' => 'The deadlift trains the entire posterior chain.',
        ],
        'core' => [
            'term' => 'Core',
            'expansion' => '',
            'category' => 'muscles',
            'definition' => 'The muscles around your midsection: abs, obliques and deep stabilizers. It stabilizes you in almost every exercise.',
            'example' => 'Planks and heavy compound lifts challenge the core hard.',
        ],

        'tdee' => [
            'term' => 'TDEE',
            'expansion' => 'Total Daily Energy Expenditure',
            'category' => 'nutrition',
            'definition' => 'Your total daily calorie burn, meaning your basal rate plus movement and daily life. The baseline for gaining or losing weight.',
            'example' => 'Eat below your TDEE and you lose weight, above it and you gain.',
        ],
        'bmr' => [
            'term' => 'BMR',
            'expansion' => 'Basal Metabolic Rate',
            'category' => 'nutrition',
            'definition' => 'The calories your body burns at complete rest just to function. Usually estimated with the Mifflin-St Jeor equation.',
            'example' => 'Your BMR makes up the largest part of your daily burn.',
        ],
        'mifflin-st-jeor' => [
            'term' => 'Mifflin-St Jeor equation',
            'expansion' => '',
            'category' => 'nutrition',
            'definition' => 'The standard formula for estimating basal metabolic rate from weight, height, age and sex. More accurate than older formulas.',
            'example' => 'Our calorie calculator uses Mifflin-St Jeor for the BMR.',
        ],
        'activity-factor' => [
            'term' => 'Activity factor',
            'expansion' => 'PAL',
            'category' => 'nutrition',
            'definition' => 'A multiplier that raises your basal rate based on daily activity to arrive at your TDEE. From about 1.2 (sedentary) to 1.9 (very active).',
            'example' => 'BMR times activity factor gives your TDEE.',
        ],
        'macros' => [
            'term' => 'Macronutrients',
            'expansion' => 'macros',
            'category' => 'nutrition',
            'definition' => 'The three energy-providing nutrients: protein, carbs and fat. Protein and carbs have about 4 kcal per gram, fat about 9.',
            'example' => 'Your macros split your daily calories across protein, carbs and fat.',
        ],
        'calorie-deficit' => [
            'term' => 'Calorie deficit',
            'expansion' => 'and surplus',
            'category' => 'nutrition',
            'definition' => 'Eating fewer calories than you burn (a deficit) leads to weight loss, more (a surplus) leads to weight gain.',
            'example' => 'A deficit of about 500 kcal a day means roughly 0.5 kg lost per week.',
        ],
        'maintenance-calories' => [
            'term' => 'Maintenance calories',
            'expansion' => '',
            'category' => 'nutrition',
            'definition' => 'The calorie intake at which your weight stays stable. It equals your TDEE.',
            'example' => 'At maintenance calories you hold your weight, neither gaining nor losing.',
        ],
        'lean-body-mass' => [
            'term' => 'Lean body mass',
            'expansion' => '',
            'category' => 'nutrition',
            'definition' => 'Your body weight without body fat, meaning muscle, bone, organs and water. Sometimes used to set protein needs.',
            'example' => 'When cutting, protein targets are often based on lean body mass.',
        ],

        'check-in' => [
            'term' => 'Check-in',
            'expansion' => 'weekly check-in',
            'category' => 'tracking',
            'definition' => 'Your short weekly ritual in the app: weight, optional measurements and how you felt. It lets your coach see real progress.',
            'example' => 'At check-in you log your weight and Mona responds to your trend.',
        ],
    ],

    'faqs' => [
        [
            'question' => 'What is a good RPE for strength training?',
            'answer' => 'For muscle growth, most working sets sit at RPE 7 to 9, meaning 1 to 3 reps in reserve. That keeps the stimulus high without wrecking your technique or over-fatiguing you.',
        ],
        [
            'question' => 'How do I read a tempo like 3-0-1-0?',
            'answer' => 'The four numbers are seconds for the phases of one rep: lowering, pause at the bottom, lifting, pause at the top. So 3-0-1-0 means lower for 3 seconds, no pause, lift for 1, no pause.',
        ],
        [
            'question' => 'What is the difference between BMR and TDEE?',
            'answer' => 'Your BMR is what you burn at complete rest. Your TDEE is your total daily burn including movement and daily life. You get from BMR to TDEE by multiplying with your activity factor.',
        ],
        [
            'question' => 'How many sets per muscle per week should I do?',
            'answer' => 'Around 10 to 20 hard sets per muscle per week is considered the productive range for muscle growth. Beginners often grow on less, spread across two sessions a week.',
        ],
    ],

    'cta' => [
        'headline' => 'Stop guessing. Get a plan that puts these terms to work for you.',
        'button' => 'Try fytrr for free',
        'trust' => 'Start free, no credit card needed.',
    ],

    'links' => [
        'planRoast' => 'Workout Plan Check: get your plan rated',
        'calorie' => 'Calorie calculator: find your TDEE',
        'macro' => 'Macro calculator: work out your macros',
        'workoutPlans' => 'Free workout plans',
    ],
];
