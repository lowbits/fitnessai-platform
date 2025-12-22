<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style type="text/css" media="all">
        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
            }

            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
            background-color: #CCDBE9;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            color: white;
            background: rgb(8, 35, 62);
            background: -moz-linear-gradient(67deg, rgba(8, 35, 62, 1) 0%, rgba(25, 31, 37, 1) 100%);
            background: -webkit-linear-gradient(67deg, rgba(8, 35, 62, 1) 0%, rgba(25, 31, 37, 1) 100%);
            background: linear-gradient(67deg, rgba(8, 35, 62, 1) 0%, rgba(25, 31, 37, 1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#08233e", endColorstr="#191f25", GradientType=1);
        }

        main {
            padding: 24px;
        }

        .plan-infos {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 5px;
        }

        .plan-infos p {
            margin: 0;
            font-size: 11px;
        }

        #logo {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        #logo h1 {
            margin: 0;
            font-size: 24px;
        }

        #logo span {
            font-size: 14px;
        }

        #content {
            font-size: 12px;
            line-height: 1.6;
            text-align: left;
            color: #12181E;
        }

        h2 {
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #08233e;
            border-bottom: 2px solid #48D670;
            padding-bottom: 5px;
        }

        h3 {
            font-size: 15px;
            margin-top: 15px;
            margin-bottom: 10px;
            color: #333;
            font-weight: bold;
        }

        h4 {
            font-size: 13px;
            margin-top: 12px;
            margin-bottom: 8px;
            color: #08233e;
        }

        .workout-card {
            page-break-inside: avoid;
            margin-bottom: 30px;
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .workout-header {
            background: linear-gradient(135deg, #48D670 0%, #56E97F 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin: -20px -20px 20px -20px;
        }

        .workout-meta {
            display: flex;
            gap: 20px;
            margin: 10px 0;
            font-size: 11px;
            flex-wrap: wrap;
        }

        .workout-meta span {
            background-color: #f0f0f0;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .exercise-item {
            page-break-inside: avoid;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #48D670;
            border-radius: 4px;
        }

        .exercise-sets {
            display: flex;
            gap: 15px;
            margin: 10px 0;
            font-size: 11px;
            color: #666;
        }

        .rest-day {
            background: linear-gradient(135deg, #9EB9D4 0%, #CCDBE9 100%);
            color: #08233e;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        .info-box {
            background-color: #e6eef5;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }

        ul, ol {
            margin: 5px 0 15px 0;
            padding-left: 30px;
        }

        li {
            margin-bottom: 5px;
        }

        hr {
            margin: 30px 0;
            border: none;
            border-top: 1px solid #ddd;
        }

        /* Page Break Control - Prevent awkward breaks */
        h2, h3, h4 {
            page-break-after: avoid;
            page-break-inside: avoid;
            orphans: 3;
            widows: 3;
        }

        .workout-day {
            page-break-before: auto;
            page-break-after: auto;
        }

        /* Don't prevent break inside exercise-list as it can be too large */
        /* But keep individual exercises together */
        .exercise-item {
            page-break-inside: avoid;
            orphans: 3;
            widows: 3;
        }

        /* Keep form cues list together with heading */
        .form-cues-section {
            page-break-inside: avoid;
        }

        ul, ol {
            orphans: 2;
            widows: 2;
        }
    </style>
</head>
<body>

<header>
    <div id="logo">
        <img src="{{public_path('apple-touch-icon.png')}}" width="40" alt="fitness ai logo">

        <div>
            <h1>Workout Plan</h1>
            <span>powered by fitnessai.me</span>
        </div>
    </div>

    <div class="plan-infos">
        <p>User: <strong>{{ $user->name }}</strong></p>
        <p>Email: <strong>{{ $user->email }}</strong></p>
        <p>Plan: <strong>{{ $plan->plan_name }}</strong></p>
        <p>Duration: <strong>{{ $plan->start_date->format('M d, Y') }} - {{ $plan->end_date->format('M d, Y') }}</strong></p>
        <p>Generated: <strong>{{ now()->format('M d, Y') }}</strong></p>
    </div>
</header>

<main>
    <div id="content">
        <h2 style="text-align: center; color: #08233e; margin-bottom: 30px;">Your Personalized {{ config('plans.duration_days', 28) }}-Day Workout Plan</h2>

        <div class="info-box">
            <h3 style="margin-top: 0;">Your Training Overview</h3>
            <ul style="list-style: none; padding-left: 0;">
                <li><strong>Goal:</strong> {{ $user->profile->body_goal?->label() ?? 'Not specified' }}</li>
                <li><strong>Workouts per Week:</strong> {{ $plan->workouts_per_week }}</li>
                <li><strong>Training Place:</strong> {{ $user->profile->training_place?->label() ?? 'Gym' }}</li>
                <li><strong>Skill Level:</strong> {{ $user->profile->skill_level?->label() ?? 'Beginner' }}</li>
            </ul>
        </div>

        @foreach($workoutPlans as $workoutPlan)
            <div class="workout-card workout-day">
                @if($workoutPlan->workout_type === 'rest')
                    <div class="rest-day">
                        <h2 style="border: none; margin: 0; color: #08233e;">
                            Day {{ $workoutPlan->day_number }} - {{ $workoutPlan->date->format('l, M d, Y') }}
                        </h2>
                        <h3 style="margin: 10px 0;">Rest & Recovery Day</h3>
                        <p style="margin: 10px 0 0 0;">Take this day to rest and allow your muscles to recover. Light stretching or walking is encouraged.</p>
                    </div>
                @elseif($workoutPlan->status === 'generated')
                    <div class="workout-header">
                        <h2 style="border: none; margin: 0; padding: 0; color: white;">
                            Day {{ $workoutPlan->day_number }} - {{ $workoutPlan->date->format('l, M d, Y') }}
                        </h2>
                        <h3 style="margin: 10px 0 0 0; color: white;">{{ $workoutPlan->workout_name }}</h3>
                    </div>

                    <div class="workout-meta">
                        <span><strong>Type:</strong> {{ ucfirst($workoutPlan->workout_type) }}</span>
                        @if($workoutPlan->difficulty)
                            <span><strong>Difficulty:</strong> {{ ucfirst($workoutPlan->difficulty) }}</span>
                        @endif
                        @if($workoutPlan->estimated_duration_minutes)
                            <span><strong>Duration:</strong> ~{{ $workoutPlan->estimated_duration_minutes }} min</span>
                        @endif
                        @if($workoutPlan->exercises->count() > 0)
                            <span><strong>Exercises:</strong> {{ $workoutPlan->exercises->count() }}</span>
                        @endif
                    </div>

                    @if($workoutPlan->description)
                        <p style="margin: 15px 0; font-style: italic; color: #555;">{{ $workoutPlan->description }}</p>
                    @endif

                    @if($workoutPlan->muscle_groups && is_array($workoutPlan->muscle_groups) && count($workoutPlan->muscle_groups) > 0)
                        <div style="margin: 15px 0;">
                            <strong>Target Muscle Groups:</strong>
                            <span style="color: #48D670;">{{ implode(', ', array_map('ucfirst', $workoutPlan->muscle_groups)) }}</span>
                        </div>
                    @endif

                    @if($workoutPlan->exercises->count() > 0)
                        <h3 style="margin-top: 25px; margin-bottom: 15px; color: #08233e; font-size: 16px; border-bottom: 2px solid #48D670; padding-bottom: 8px;">Exercises</h3>

                        @foreach($workoutPlan->exercises as $exercise)
                            <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse; border: 1px solid #ddd;">
                                <thead>
                                    <tr>
                                        <th colspan="2" style="text-align: left; padding: 16px 18px; background-color: #08233e; color: white; font-size: 13px; font-weight: bold; border-bottom: 3px solid #48D670;">
                                            {{ $loop->iteration }}. {{ $exercise->name }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody style="background-color: #ffffff;">
                                    <tr>
                                        <td colspan="2" style="padding: 12px 18px; background-color: #f9f9f9; border-bottom: 1px solid #e0e0e0;">
                                            <table style="width: 100%; border-collapse: collapse;">
                                                <tr>
                                                    @if($exercise->sets)
                                                        <td style="padding: 5px 15px 5px 0; font-size: 11px; color: #333;">
                                                            <strong>Sets:</strong> {{ $exercise->sets }}
                                                        </td>
                                                    @endif
                                                    @if($exercise->reps)
                                                        <td style="padding: 5px 15px 5px 0; font-size: 11px; color: #333;">
                                                            <strong>Reps:</strong> {{ $exercise->reps }}
                                                        </td>
                                                    @endif
                                                    @if($exercise->duration_seconds)
                                                        <td style="padding: 5px 15px 5px 0; font-size: 11px; color: #333;">
                                                            <strong>Duration:</strong> {{ $exercise->duration_seconds }}s
                                                        </td>
                                                    @endif
                                                    @if($exercise->rest_seconds)
                                                        <td style="padding: 5px 0; font-size: 11px; color: #333;">
                                                            <strong>Rest:</strong> {{ $exercise->rest_seconds }}s
                                                        </td>
                                                    @endif
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    @if($exercise->description)
                                        <tr>
                                            <td colspan="2" style="padding: 12px 18px; color: #666; font-size: 11px; line-height: 1.6; border-bottom: 1px solid #e0e0e0;">
                                                {{ $exercise->description }}
                                            </td>
                                        </tr>
                                    @endif

                                    @if($exercise->form_cues && is_array($exercise->form_cues) && count($exercise->form_cues) > 0)
                                        <tr>
                                            <td colspan="2" style="padding: 15px 18px; vertical-align: top; background-color: #fff; border-bottom: 1px solid #e0e0e0;">
                                                <strong style="color: #08233e; font-size: 12px; display: block; margin-bottom: 8px;">Form Cues</strong>
                                                <table style="width: 100%; border-collapse: collapse;">
                                                    @foreach($exercise->form_cues as $cue)
                                                        <tr>
                                                            <td style="padding: 3px 0; font-size: 11px; vertical-align: top; width: 15px; color: #999;">•</td>
                                                            <td style="padding: 3px 0; font-size: 11px; color: #333; line-height: 1.5;">
                                                                {{ is_string($cue) ? $cue : json_encode($cue) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </td>
                                        </tr>
                                    @endif

                                    @if($exercise->equipment && is_array($exercise->equipment) && count($exercise->equipment) > 0)
                                        <tr>
                                            <td colspan="2" style="padding: 12px 18px; font-size: 11px; background-color: #f9f9f9; border-bottom: 1px solid #e0e0e0;">
                                                <strong style="color: #333;">Equipment:</strong>
                                                <span style="color: #666; margin-left: 8px;">{{ implode(', ', array_map(function($item) { return is_string($item) ? $item : json_encode($item); }, $exercise->equipment)) }}</span>
                                            </td>
                                        </tr>
                                    @endif

                                    @if($exercise->alternatives && is_array($exercise->alternatives) && count($exercise->alternatives) > 0)
                                        <tr>
                                            <td colspan="2" style="padding: 12px 18px; font-size: 11px; background-color: #f9f9f9;">
                                                <strong style="color: #333;">Alternatives:</strong>
                                                <span style="color: #666; margin-left: 8px;">{{ implode(', ', array_map(function($item) { return is_string($item) ? $item : json_encode($item); }, $exercise->alternatives)) }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        @endforeach
                    @endif

                    @if($workoutPlan->notes)
                        <div class="info-box" style="margin-top: 20px;">
                            <strong>Notes:</strong>
                            <p style="margin: 5px 0 0 0;">{{ $workoutPlan->notes }}</p>
                        </div>
                    @endif
                @else
                    <h2>Day {{ $workoutPlan->day_number }} - {{ $workoutPlan->date->format('l, M d, Y') }}</h2>
                    <p style="color: #999; font-style: italic;">Workout plan for this day is being generated...</p>
                @endif
            </div>

            <hr>
        @endforeach

        <div style="margin-top: 40px; padding: 20px; background-color: #e6eef5; border-radius: 8px; text-align: center;">
            <p style="margin: 0; font-size: 14px;"><strong>Need help or have questions?</strong></p>
            <p style="margin: 5px 0 0 0;">Contact us at <strong>hello@fitnessai.me</strong></p>
            <p style="margin: 10px 0 0 0; font-size: 11px; color: #666;">
                Tip: Always warm up before starting your workout and cool down afterwards!
            </p>
        </div>
    </div>
</main>

</body>
</html>

