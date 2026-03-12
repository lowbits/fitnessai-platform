<?php

// TEST OUTPUT: Generated using fytrr-workout-plan skill
// Slug: strength | Goal: Pure strength training / powerlifting-adjacent
// Schedule: Upper-Lower split, 4 days/week — differentiated from existing Push/Pull/Legs/Core


'strength' => [
    'title'           => 'Free Strength Training Plan – 10 Weeks',
    'description'     => 'Free 10-week strength training plan. Progressive overload-based program to build maximum strength in squat, deadlift, bench and overhead press.',
    'h1'              => 'Strength Training Plan – Build Real, Measurable Strength',
    'intro'           => 'This 10-week strength training plan uses an upper-lower split and progressive overload to systematically increase your one-rep max across the four fundamental lifts. Designed for gym training with barbells, it develops maximal strength, neuromuscular efficiency and structural resilience over 10 structured weeks.',
    'internal_type'   => 'strength',
    'published_at'    => '2026-03-12',
    'last_updated_at' => '2026-03-12',

    'workout' => [
        'weeks'             => 10,
        'workouts_per_week' => 4,
        'duration_minutes'  => 60,
        'level'             => 'Intermediate to Advanced',
        'equipment'         => ['Barbell', 'Power Rack', 'Bench', 'Dumbbells', 'Weight Plates'],

        'schedule' => [
            [
                'day'   => 'Day 1 – Lower Body Strength',
                'focus' => 'Squat & deadlift pattern — maximal lower body force production',
                'exercises' => [
                    ['name' => 'Back Squat',           'sets' => 5, 'reps' => '3–5',      'rest' => '180s'],
                    ['name' => 'Romanian Deadlift',    'sets' => 4, 'reps' => '5–6',      'rest' => '120s'],
                    ['name' => 'Leg Press',            'sets' => 3, 'reps' => '8–10',     'rest' => '90s'],
                    ['name' => 'Nordic Curl or Leg Curl', 'sets' => 3, 'reps' => '6–8',  'rest' => '90s'],
                    ['name' => 'Calf Raises',          'sets' => 3, 'reps' => '12–15',    'rest' => '60s'],
                ],
            ],
            [
                'day'   => 'Day 2 – Upper Body Strength',
                'focus' => 'Bench press & row — horizontal push and pull strength',
                'exercises' => [
                    ['name' => 'Barbell Bench Press',  'sets' => 5, 'reps' => '3–5',      'rest' => '180s'],
                    ['name' => 'Barbell Bent-Over Row', 'sets' => 4, 'reps' => '4–6',    'rest' => '120s'],
                    ['name' => 'Incline Dumbbell Press','sets' => 3, 'reps' => '8–10',    'rest' => '90s'],
                    ['name' => 'Seated Cable Row',     'sets' => 3, 'reps' => '8–10',     'rest' => '90s'],
                    ['name' => 'Triceps Pushdowns',    'sets' => 3, 'reps' => '10–12',    'rest' => '60s'],
                ],
            ],
            [
                'day'   => 'Day 3 – Lower Body Power',
                'focus' => 'Deadlift & squat variation — posterior chain & explosive strength',
                'exercises' => [
                    ['name' => 'Conventional Deadlift', 'sets' => 5, 'reps' => '3–5',    'rest' => '180s'],
                    ['name' => 'Front Squat or Goblet Squat', 'sets' => 3, 'reps' => '5–8', 'rest' => '120s'],
                    ['name' => 'Bulgarian Split Squat', 'sets' => 3, 'reps' => '6–8 per leg', 'rest' => '90s'],
                    ['name' => 'Glute-Ham Raise or Hip Thrust', 'sets' => 3, 'reps' => '8–10', 'rest' => '90s'],
                    ['name' => 'Ab Wheel or Hanging Leg Raise', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                ],
            ],
            [
                'day'   => 'Day 4 – Upper Body Power',
                'focus' => 'Overhead press & vertical pull — shoulder and back strength',
                'exercises' => [
                    ['name' => 'Overhead Press (Barbell)', 'sets' => 5, 'reps' => '3–5', 'rest' => '180s'],
                    ['name' => 'Weighted Pull-ups or Lat Pulldown', 'sets' => 4, 'reps' => '4–6', 'rest' => '120s'],
                    ['name' => 'Dumbbell Shoulder Press', 'sets' => 3, 'reps' => '8–10', 'rest' => '90s'],
                    ['name' => 'Face Pulls',             'sets' => 3, 'reps' => '12–15',  'rest' => '60s'],
                    ['name' => 'Barbell or Dumbbell Curls', 'sets' => 3, 'reps' => '8–10', 'rest' => '60s'],
                ],
            ],
        ],

        'progression' => 'Weeks 1–2: Establish technique and baseline loads | Weeks 3–5: Linear progression, add 2.5–5 kg per session | Weeks 6–8: Volume phase, higher reps at 75–80% 1RM | Weeks 9–10: Peaking phase, heavy singles and doubles',
        'tips' => [
            'Eat at a slight calorie surplus of 200–400 kcal to support strength adaptation',
            'Consume 2.0–2.2 g of protein per kg of bodyweight daily to maximise muscle protein synthesis',
            'Rest 3–5 minutes between your heaviest sets — strength demands full neuromuscular recovery',
            'Track every session: weight, sets and reps. You cannot manage what you do not measure',
            'Sleep 8 hours per night — testosterone and growth hormone secretion peak during deep sleep',
        ],
    ],

    'why_it_works' => [
        'title'   => 'Why This Strength Training Plan Works',
        'content' => [
            [
                'heading' => 'Progressive Overload Is the Engine of Strength Gains',
                'text'    => 'Strength improves only when your neuromuscular system is exposed to loads it has not previously handled. This plan is built around linear and block-based progressive overload — systematically increasing the weight or volume each week — which strength science consistently identifies as the primary driver of maximal force production. Without it, training becomes maintenance.',
            ],
            [
                'heading' => 'Low-Rep, High-Load Training Builds the Nervous System, Not Just Muscle',
                'text'    => 'Sets of 3–5 reps at 80–90% of your one-rep max teach your nervous system to recruit more motor units simultaneously. This neuromuscular adaptation accounts for a significant portion of early strength gains — often more than muscle hypertrophy. That is why experienced athletes can be dramatically stronger than athletes of similar size.',
            ],
            [
                'heading' => 'The Upper-Lower Split Maximises Recovery Without Sacrificing Frequency',
                'text'    => 'Each muscle group is trained twice per week — the minimum frequency research associates with optimal strength development. By separating upper and lower days, you allow 48–72 hours of recovery between sessions targeting the same muscles, which is the window in which adaptation and repair occur.',
            ],
            [
                'heading' => 'Compound Movements Produce the Highest Hormonal Response',
                'text'    => 'Squats, deadlifts, bench press and overhead press activate the largest total muscle mass of any exercises. This recruits more motor units, creates greater mechanical tension across the whole body, and produces the strongest anabolic hormonal response — all of which accelerate strength development far beyond isolation exercises alone.',
            ],
            [
                'heading' => 'Peaking Weeks Convert Training Volume Into Tested Strength',
                'text'    => 'The final two weeks shift from volume to intensity — reducing total sets while increasing load. This peaking approach, standard in powerlifting periodisation, allows accumulated fatigue to dissipate while preserving the strength adaptations built over the prior eight weeks. The result is peak performance exactly when you need it.',
            ],
        ],
    ],

    'common_mistakes' => [
        'title'    => 'The 7 Most Common Strength Training Mistakes — and How to Avoid Them',
        'mistakes' => [
            [
                'title'       => 'Adding Weight Too Quickly',
                'problem'     => 'Jumping 10–20 kg between sessions out of impatience.',
                'consequence' => 'Form breaks down, the lift stalls early, and injury risk rises sharply — often ending the training cycle prematurely.',
                'solution'    => 'Add 2.5 kg per session on upper body lifts and 5 kg on lower body lifts. Small, consistent increments compound dramatically over 10 weeks.',
                'example'     => 'Bench press at 80 kg: add 2.5 kg per session → 107.5 kg after 11 sessions, not 120 kg after 2.',
            ],
            [
                'title'       => 'Skipping the Warm-Up Sets',
                'problem'     => 'Going directly to working weight without building up.',
                'consequence' => 'Cold muscles and unprepared connective tissue lead to poor bar speed, technique breakdown and acute injury.',
                'solution'    => 'Work up with 3–4 warm-up sets: 40%, 60%, 80% of working weight before your first true set.',
                'example'     => 'Working weight 120 kg squat: warm up at 60 kg × 5, 80 kg × 3, 100 kg × 1, then start working sets.',
            ],
            [
                'title'       => 'Cutting Rest Periods Short',
                'problem'     => 'Resting 60–90 seconds between heavy sets because it "feels like enough".',
                'consequence' => 'Phosphocreatine stores — the primary energy system for maximal effort — are not fully replenished. Performance drops each set and total training quality collapses.',
                'solution'    => 'Rest 3–5 minutes between working sets of 1–5 reps. Use a timer, not your perception.',
                'example'     => 'After a 5-rep set at 90% 1RM, set a 3-minute timer before touching the bar again.',
            ],
            [
                'title'       => 'Neglecting Technique Under Heavy Load',
                'problem'     => 'Accepting a "good enough" squat or deadlift to hit a new number.',
                'consequence' => 'Repeated technical failure under load creates chronic injury patterns — particularly in the lower back, knees and shoulders.',
                'solution'    => 'If you cannot maintain the same technique at 90% that you use at 70%, the weight is too heavy. Reduce load and rebuild.',
                'example'     => 'If your lower back rounds on the deadlift at 140 kg but not 120 kg, train at 120 kg until 140 kg looks identical.',
            ],
            [
                'title'       => 'Eating at Maintenance or a Deficit',
                'problem'     => 'Trying to get stronger while losing weight simultaneously.',
                'consequence' => 'The body prioritises survival over adaptation. Strength gains stall within 3–4 weeks and recovery suffers.',
                'solution'    => 'Eat at a modest calorie surplus of 200–400 kcal per day. This provides energy for recovery without excessive fat gain.',
                'example'     => 'At a maintenance level of 2,600 kcal, target 2,800–3,000 kcal — not 2,200 kcal.',
            ],
            [
                'title'       => 'Not Tracking Workouts',
                'problem'     => 'Training from memory, estimating what was lifted last session.',
                'consequence' => 'You cannot apply progressive overload consistently without records. Progress becomes random and stalls.',
                'solution'    => 'Log every set: exercise, weight, reps achieved. Review before each session to set your target.',
                'example'     => 'A simple notes app or training log: "Squat: 100 kg × 5, 5, 4 → next session: 102.5 kg".',
            ],
            [
                'title'       => 'Training Through Pain Instead of Discomfort',
                'problem'     => 'Confusing sharp, joint-specific pain with normal training fatigue.',
                'consequence' => 'Acute injuries become chronic. A missed week becomes a missed month.',
                'solution'    => 'Distinguish between muscular fatigue (acceptable) and joint or tendon pain (stop immediately). Reduce load, address technique and consult a physiotherapist if pain persists.',
                'example'     => 'Burning quads after squats = normal. Sharp knee pain during the lift = stop, assess, do not push through.',
            ],
        ],
        'summary' => 'Strength training fails not because of effort, but because of impatience, poor recovery and neglected technique — this plan eliminates all three with structure and progression.',
    ],

    'faqs' => [
        [
            'question' => 'How heavy should I lift to build strength?',
            'answer'   => 'Work in the 80–90% of your one-rep max range for your main lifts, performing 3–5 reps per set. This load range produces the greatest neuromuscular adaptations for strength.',
        ],
        [
            'question' => 'Can beginners follow a strength training plan?',
            'answer'   => 'This plan is best suited to those with at least 3–6 months of consistent training. Beginners benefit more from higher-rep full-body programs first before moving to strength-focused periodisation.',
        ],
        [
            'question' => 'How long until I see strength gains?',
            'answer'   => 'Neuromuscular improvements — better motor unit recruitment — appear within 2–3 weeks. Measurable increases in your one-rep max typically follow after 4–6 weeks of consistent progressive overload.',
        ],
    ],
],
