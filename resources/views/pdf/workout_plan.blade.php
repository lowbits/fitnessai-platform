<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('pdf.partials.fonts')

    <style type="text/css" media="all">
        @page {
            size: A4;
            /* Reserve bottom space on every page for the fixed download footer */
            margin: 0 0 74px 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            width: 100%;
            padding: 0;
            margin: 0;
            background-color: #E9EFF5;
            font-family: "Nunito", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #12181E;
        }

        /* ---------- Cover header ---------- */
        .cover {
            background-color: #08233E;
            color: #ffffff;
            padding: 30px 36px 26px 36px;
            border-bottom: 4px solid #48D670;
        }

        .cover__table {
            width: 100%;
            border-collapse: collapse;
        }

        .cover__logo-cell {
            vertical-align: middle;
        }

        .cover__logo-cell img {
            vertical-align: middle;
        }

        .cover__brand {
            display: inline-block;
            vertical-align: middle;
            padding-left: 14px;
        }

        .cover__brand h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 0.2px;
        }

        .cover__brand span {
            display: block;
            font-size: 11px;
            color: #9EC2E6;
            margin-top: 2px;
            letter-spacing: 0.3px;
        }

        .cover__meta-cell {
            vertical-align: middle;
            text-align: right;
        }

        .cover__meta {
            border-collapse: collapse;
            margin-left: auto;
        }

        .cover__meta td {
            padding: 1px 0;
            font-size: 10.5px;
            vertical-align: top;
        }

        .cover__meta .label {
            color: #9EC2E6;
            padding-right: 10px;
            text-align: right;
        }

        .cover__meta .value {
            color: #ffffff;
            font-weight: bold;
            text-align: right;
        }

        /* ---------- Main ---------- */
        main {
            padding: 28px 36px 8px 36px;
        }

        .hero-title {
            text-align: center;
            color: #08233E;
            font-size: 20px;
            font-weight: bold;
            margin: 4px 0 22px 0;
            letter-spacing: 0.2px;
        }

        /* ---------- Overview panel ---------- */
        .overview {
            background: #F1F6FB;
            border: 1px solid #E3EAF2;
            border-radius: 12px;
            padding: 18px 22px;
            margin-bottom: 30px;
        }

        .overview__heading {
            font-size: 13px;
            font-weight: bold;
            color: #08233E;
            margin: 0 0 14px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-row {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
        }

        .stat {
            background: #ffffff;
            border: 1px solid #E3EAF2;
            border-radius: 10px;
            text-align: center;
            padding: 12px 6px;
        }

        .stat.stat--primary {
            background: #08233E;
            border-color: #08233E;
        }

        .stat__value {
            display: block;
            font-size: 15px;
            font-weight: bold;
            color: #08233E;
            line-height: 1.15;
        }

        .stat--primary .stat__value {
            color: #48D670;
        }

        .stat__label {
            display: block;
            font-size: 9px;
            color: #647488;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 4px;
        }

        .stat--primary .stat__label {
            color: #9EC2E6;
        }

        /* ---------- Day / workout card ---------- */
        .workout-day {
            margin-bottom: 26px;
        }

        /* Day heading — shared style with the meal plan */
        .day-heading {
            font-size: 17px;
            font-weight: bold;
            color: #08233E;
            margin: 30px 0 14px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #48D670;
            letter-spacing: 0.2px;
            page-break-after: avoid;
        }

        .workout-card {
            border: 1px solid #E3EAF2;
            border-radius: 12px;
            background-color: #ffffff;
            padding: 18px 20px;
        }

        .workout-name {
            font-size: 16px;
            font-weight: bold;
            color: #08233E;
            margin: 0 0 14px 0;
        }

        .workout-meta {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 14px;
        }

        .workout-meta td {
            background-color: #F1F6FB;
            border: 1px solid #E3EAF2;
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 10px;
            color: #3A4A5A;
            text-align: center;
        }

        .workout-meta strong {
            color: #08233E;
        }

        .workout-desc {
            margin: 0 0 14px 0;
            font-style: italic;
            color: #647488;
            font-size: 11px;
            line-height: 1.6;
        }

        .muscle-groups {
            margin: 0 0 16px 0;
            font-size: 11px;
            color: #3A4A5A;
        }

        .muscle-groups strong {
            color: #08233E;
        }

        .muscle-groups .accent {
            color: #2BB673;
            font-weight: bold;
        }

        .exercises-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #08233E;
            margin: 18px 0 12px 0;
            padding-bottom: 6px;
            border-bottom: 2px solid #48D670;
        }

        /* ---------- Exercise (compact list) ---------- */
        .ex {
            page-break-inside: avoid;
            padding: 12px 0;
            border-top: 1px solid #EEF2F7;
        }

        .ex.first {
            border-top: none;
            padding-top: 2px;
        }

        .ex__head {
            width: 100%;
            border-collapse: collapse;
        }

        .ex__num {
            width: 22px;
            vertical-align: top;
            font-size: 12.5px;
            font-weight: bold;
            color: #48D670;
        }

        .ex__name {
            vertical-align: top;
            font-size: 12.5px;
            font-weight: bold;
            color: #08233E;
            line-height: 1.35;
        }

        .ex__stats {
            margin: 7px 0 0 22px;
        }

        .ex__pill {
            display: inline-block;
            background: #F1F6FB;
            border: 1px solid #E3EAF2;
            border-radius: 20px;
            padding: 3px 9px;
            font-size: 9px;
            color: #647488;
            margin-right: 5px;
        }

        .ex__pill strong {
            color: #08233E;
            font-size: 10px;
        }

        .ex__desc {
            margin: 8px 0 0 22px;
            font-size: 10.5px;
            color: #647488;
            line-height: 1.5;
        }

        .ex__line {
            margin: 6px 0 0 22px;
            font-size: 10px;
            color: #7A8896;
            line-height: 1.5;
        }

        .ex__line .lbl {
            color: #08233E;
            font-weight: bold;
        }

        .ex__cue {
            color: #3A4A5A;
        }

        .notes-box {
            background: #F1F6FB;
            border: 1px solid #E3EAF2;
            border-radius: 8px;
            padding: 13px 16px;
            margin-top: 16px;
            font-size: 11px;
        }

        .notes-box strong {
            color: #08233E;
        }

        /* ---------- Rest day ---------- */
        .rest-day {
            border: 1px solid #D4E0EC;
            background: #F1F6FB;
            color: #08233E;
            padding: 26px;
            border-radius: 12px;
            text-align: center;
            page-break-inside: avoid;
        }

        .rest-day .day-label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #647488;
        }

        .rest-day .rest-title {
            display: block;
            font-size: 17px;
            font-weight: bold;
            margin: 6px 0;
        }

        .rest-day p {
            margin: 6px 0 0 0;
            font-size: 11.5px;
            color: #647488;
        }

        .generating {
            color: #99A7B5;
            font-style: italic;
        }

        /* ---------- Closing help box ---------- */
        .help-box {
            margin-top: 34px;
            padding: 20px;
            background: #F1F6FB;
            border: 1px solid #E3EAF2;
            border-radius: 12px;
            text-align: center;
        }

        .help-box p {
            margin: 0;
            font-size: 13px;
        }

        .help-box .contact {
            margin-top: 5px;
            color: #647488;
        }

        .help-box .contact strong {
            color: #08233E;
        }

        p {
            orphans: 2;
            widows: 2;
        }
    </style>
</head>
<body>

<div class="cover">
    <table class="cover__table">
        <tr>
            <td class="cover__logo-cell">
                <img src="{{ public_path('apple-touch-icon.png') }}" width="44" height="44" alt="fytrr">
                <span class="cover__brand">
                    <h1>{{ __('pdf.workout_plan.title') }}</h1>
                    <span>{{ __('pdf.workout_plan.powered_by') }}</span>
                </span>
            </td>
            <td class="cover__meta-cell">
                <table class="cover__meta">
                    <tr><td class="label">{{ __('pdf.workout_plan.user') }}</td><td class="value">{{ $user->name }}</td></tr>
                    <tr><td class="label">{{ __('pdf.workout_plan.plan') }}</td><td class="value">{{ $plan->plan_name }}</td></tr>
                    <tr><td class="label">{{ __('pdf.workout_plan.duration') }}</td><td class="value">{{ $plan->start_date->translatedFormat('M d, Y') }} &ndash; {{ $plan->end_date->translatedFormat('M d, Y') }}</td></tr>
                    <tr><td class="label">{{ __('pdf.workout_plan.generated') }}</td><td class="value">{{ now()->translatedFormat('M d, Y') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<main>
    <div class="hero-title">{{ __('pdf.workout_plan.personalized_title', ['days' => config('plans.duration_days', 28)]) }}</div>

    <div class="overview">
        <p class="overview__heading">{{ __('pdf.workout_plan.training_overview') }}</p>
        <table class="stat-row">
            <tr>
                <td class="stat stat--primary" width="25%">
                    <span class="stat__value">{{ $plan->workouts_per_week }}</span>
                    <span class="stat__label">{{ __('pdf.workout_plan.workouts_per_week') }}</span>
                </td>
                <td class="stat" width="25%">
                    <span class="stat__value">{{ $user->profile->body_goal?->label() ?? __('pdf.workout_plan.not_specified') }}</span>
                    <span class="stat__label">{{ __('pdf.workout_plan.goal') }}</span>
                </td>
                <td class="stat" width="25%">
                    <span class="stat__value">{{ $user->profile->training_place?->label() ?? 'Gym' }}</span>
                    <span class="stat__label">{{ __('pdf.workout_plan.training_place') }}</span>
                </td>
                <td class="stat" width="25%">
                    <span class="stat__value">{{ $user->profile->skill_level?->label() ?? 'Beginner' }}</span>
                    <span class="stat__label">{{ __('pdf.workout_plan.skill_level') }}</span>
                </td>
            </tr>
        </table>
    </div>

    @foreach($workoutPlans as $workoutPlan)
        <div class="workout-day">
            <div class="day-heading">{{ __('pdf.workout_plan.day') }} {{ $workoutPlan->day_number }} &middot; {{ $workoutPlan->date->translatedFormat('l, M d, Y') }}</div>

            @if($workoutPlan->workout_type === 'rest')
                <div class="rest-day">
                    <span class="rest-title">{{ __('pdf.workout_plan.rest_day') }}</span>
                    <p>{{ __('pdf.workout_plan.rest_description') }}</p>
                </div>
            @elseif($workoutPlan->status === 'generated')
                <div class="workout-card">
                    <div class="workout-name">{{ $workoutPlan->workout_name }}</div>

                    <table class="workout-meta">
                            <tr>
                                <td><strong>{{ __('pdf.workout_plan.type') }}</strong><br>{{ ucfirst($workoutPlan->workout_type) }}</td>
                                @if($workoutPlan->difficulty)
                                    <td><strong>{{ __('pdf.workout_plan.difficulty') }}</strong><br>{{ ucfirst($workoutPlan->difficulty) }}</td>
                                @endif
                                @if($workoutPlan->estimated_duration_minutes)
                                    <td><strong>{{ __('pdf.workout_plan.duration') }}</strong><br>~{{ $workoutPlan->estimated_duration_minutes }} min</td>
                                @endif
                                @if($workoutPlan->exercises->count() > 0)
                                    <td><strong>{{ __('pdf.workout_plan.exercises') }}</strong><br>{{ $workoutPlan->exercises->count() }}</td>
                                @endif
                            </tr>
                        </table>

                        @if($workoutPlan->description)
                            <p class="workout-desc">{{ $workoutPlan->description }}</p>
                        @endif

                        @if($workoutPlan->muscle_groups && is_array($workoutPlan->muscle_groups) && count($workoutPlan->muscle_groups) > 0)
                            <p class="muscle-groups">
                                <strong>{{ __('pdf.workout_plan.target_muscle_groups') }}:</strong>
                                <span class="accent">{{ implode(', ', array_map('ucfirst', $workoutPlan->muscle_groups)) }}</span>
                            </p>
                        @endif

                        @if($workoutPlan->exercises->count() > 0)
                            <div class="exercises-title">{{ __('pdf.workout_plan.exercises') }}</div>

                            @foreach($workoutPlan->exercises as $exercise)
                                <div class="ex @if($loop->first) first @endif">
                                    <table class="ex__head">
                                        <tr>
                                            <td class="ex__num">{{ $loop->iteration }}</td>
                                            <td class="ex__name">{{ $exercise->exercise?->localizedName() ?? $exercise->name }}</td>
                                        </tr>
                                    </table>

                                    @if($exercise->sets || $exercise->reps || $exercise->duration_seconds || $exercise->rest_seconds)
                                        <div class="ex__stats">
                                            @if($exercise->sets)<span class="ex__pill"><strong>{{ $exercise->sets }}</strong> {{ __('pdf.workout_plan.sets') }}</span>@endif
                                            @if($exercise->reps)<span class="ex__pill"><strong>{{ $exercise->reps }}</strong> {{ __('pdf.workout_plan.reps') }}</span>@endif
                                            @if($exercise->duration_seconds)<span class="ex__pill"><strong>{{ $exercise->duration_seconds }}s</strong> {{ __('pdf.workout_plan.duration') }}</span>@endif
                                            @if($exercise->rest_seconds)<span class="ex__pill"><strong>{{ $exercise->rest_seconds }}s</strong> {{ __('pdf.workout_plan.rest') }}</span>@endif
                                        </div>
                                    @endif

                                    @if($exercise->description)
                                        <div class="ex__desc">{{ $exercise->description }}</div>
                                    @endif

                                    @if($exercise->form_cues && is_array($exercise->form_cues) && count($exercise->form_cues) > 0)
                                        <div class="ex__line"><span class="lbl">{{ __('pdf.workout_plan.form_cues') }}:</span>
                                            <span class="ex__cue">{{ implode('  ·  ', array_map(fn ($cue) => is_string($cue) ? $cue : json_encode($cue), $exercise->form_cues)) }}</span>
                                        </div>
                                    @endif

                                    @if($exercise->equipment && is_array($exercise->equipment) && count($exercise->equipment) > 0)
                                        <div class="ex__line"><span class="lbl">{{ __('pdf.workout_plan.equipment') }}:</span>
                                            {{ implode(', ', array_map(fn ($item) => is_string($item) ? $item : json_encode($item), $exercise->equipment)) }}
                                        </div>
                                    @endif

                                    @if($exercise->alternatives && is_array($exercise->alternatives) && count($exercise->alternatives) > 0)
                                        <div class="ex__line"><span class="lbl">{{ __('pdf.workout_plan.alternatives') }}:</span>
                                            {{ implode(', ', array_map(fn ($item) => is_string($item) ? $item : ($item['name'] ?? json_encode($item)), $exercise->alternatives)) }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        @if($workoutPlan->notes)
                            <div class="notes-box">
                                <strong>{{ __('pdf.workout_plan.notes') }}:</strong>
                                <p style="margin: 5px 0 0 0;">{{ $workoutPlan->notes }}</p>
                            </div>
                        @endif
                </div>
            @else
                <div class="workout-card">
                    <p class="generating">{{ __('pdf.workout_plan.generating') }}</p>
                </div>
            @endif
        </div>
    @endforeach

    <div class="help-box">
        <p><strong>{{ __('pdf.workout_plan.help') }}</strong></p>
        <p class="contact">{{ __('pdf.workout_plan.contact') }} <strong>hello@fytrr.com</strong></p>
    </div>
</main>

@include('pdf.partials.app_footer')

</body>
</html>
