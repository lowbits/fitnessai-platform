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

        /* ---------- Targets panel ---------- */
        .targets {
            background: #F1F6FB;
            border: 1px solid #E3EAF2;
            border-radius: 12px;
            padding: 18px 22px;
            margin-bottom: 30px;
        }

        .targets__heading {
            font-size: 13px;
            font-weight: bold;
            color: #08233E;
            margin: 0 0 14px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .targets__profile {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .targets__profile td {
            font-size: 11px;
            color: #3A4A5A;
            padding: 2px 16px 2px 0;
        }

        .targets__profile .k {
            color: #647488;
        }

        .macro-row {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
        }

        .macro {
            background: #ffffff;
            border: 1px solid #E3EAF2;
            border-radius: 10px;
            text-align: center;
            padding: 10px 6px;
        }

        .macro.macro--primary {
            background: #08233E;
            border-color: #08233E;
        }

        .macro__value {
            display: block;
            font-size: 16px;
            font-weight: bold;
            color: #08233E;
            line-height: 1.1;
        }

        .macro--primary .macro__value {
            color: #ffffff;
        }

        .macro__label {
            display: block;
            font-size: 9px;
            color: #647488;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 4px;
        }

        .macro--primary .macro__label {
            color: #9EC2E6;
        }

        /* ---------- Day headings ---------- */
        .day-container {
            margin-bottom: 26px;
        }

        .day-heading {
            font-size: 17px;
            font-weight: bold;
            color: #08233E;
            margin: 30px 0 16px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #48D670;
            letter-spacing: 0.2px;
            page-break-after: avoid;
        }

        /* ---------- Meal card ---------- */
        .meal-card {
            width: 100%;
            margin-bottom: 18px;
            border-collapse: collapse;
            border: 1px solid #E3EAF2;
            border-radius: 10px;
            overflow: hidden;
            /* Keep a meal on a single page so its navy header never splits
               (a split table loses the header background = white-on-white text) */
            page-break-inside: avoid;
        }

        .meal-card__head th {
            text-align: left;
            padding: 13px 18px;
            background-color: #08233E;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 3px solid #48D670;
            page-break-after: avoid;
        }

        .meal-card__type {
            color: #48D670;
        }

        .meal-card td {
            background-color: #ffffff;
        }

        .macro-strip {
            padding: 12px 18px;
            background-color: #F7FAFD;
            border-bottom: 1px solid #EEF2F7;
        }

        .macro-strip table {
            width: 100%;
            border-collapse: collapse;
        }

        .chip {
            font-size: 10.5px;
            color: #2C4257;
        }

        .chip strong {
            color: #08233E;
        }

        .meta-line {
            padding: 9px 18px;
            background-color: #F7FAFD;
            border-bottom: 1px solid #EEF2F7;
            font-size: 10.5px;
            color: #647488;
        }

        .meta-line strong {
            color: #3A4A5A;
        }

        .meal-desc {
            padding: 13px 18px;
            font-style: italic;
            color: #647488;
            font-size: 11px;
            line-height: 1.6;
            border-bottom: 1px solid #EEF2F7;
        }

        .section-cell {
            padding: 15px 18px;
            vertical-align: top;
            border-bottom: 1px solid #EEF2F7;
        }

        .section-title {
            color: #08233E;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 10px;
        }

        .list-table {
            width: 100%;
            border-collapse: collapse;
        }

        .list-table td {
            font-size: 11px;
            color: #3A4A5A;
            line-height: 1.55;
            padding: 3px 0;
        }

        .list-table .bullet {
            width: 16px;
            color: #48D670;
            vertical-align: top;
            font-weight: bold;
        }

        .list-table .num {
            width: 26px;
            color: #08233E;
            font-weight: bold;
            vertical-align: top;
        }

        .list-table .amount {
            color: #647488;
        }

        .allergens {
            padding: 11px 18px;
            background-color: #FFF8E6;
            border-left: 4px solid #F5C443;
        }

        .allergens strong,
        .allergens span {
            color: #8A6D1B;
            font-size: 10.5px;
        }

        /* ---------- Daily totals ---------- */
        .daily-totals {
            background: #08233E;
            color: #ffffff;
            padding: 11px 18px;
            border-radius: 8px;
            margin: 6px 0 4px 0;
            font-size: 11.5px;
            page-break-inside: avoid;
        }

        .daily-totals strong {
            color: #48D670;
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

        /* ---------- Page break control ---------- */
        .nutrition-info {
            page-break-inside: avoid;
        }

        ul li, ol li {
            page-break-inside: avoid;
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
                    <h1>{{ __('pdf.nutrition_plan.title') }}</h1>
                    <span>{{ __('pdf.nutrition_plan.powered_by') }}</span>
                </span>
            </td>
            <td class="cover__meta-cell">
                <table class="cover__meta">
                    <tr><td class="label">{{ __('pdf.nutrition_plan.user') }}</td><td class="value">{{ $user->name }}</td></tr>
                    <tr><td class="label">{{ __('pdf.nutrition_plan.plan') }}</td><td class="value">{{ $plan->plan_name }}</td></tr>
                    <tr><td class="label">{{ __('pdf.nutrition_plan.duration') }}</td><td class="value">{{ $plan->start_date->translatedFormat('M d, Y') }} &ndash; {{ $plan->end_date->translatedFormat('M d, Y') }}</td></tr>
                    <tr><td class="label">{{ __('pdf.nutrition_plan.generated') }}</td><td class="value">{{ now()->translatedFormat('M d, Y') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<main>
    <div class="hero-title">{{ __('pdf.nutrition_plan.personalized_title', ['days' => $plan->duration_days]) }}</div>

    <div class="targets">
        <p class="targets__heading">{{ __('pdf.nutrition_plan.daily_targets') }}</p>

        <table class="targets__profile">
            <tr>
                <td><span class="k">{{ __('pdf.nutrition_plan.goal') }}:</span> <strong>{{ $user->profile->body_goal?->label() ?? __('pdf.nutrition_plan.not_specified') }}</strong></td>
                <td><span class="k">{{ __('pdf.nutrition_plan.diet_type') }}:</span> <strong>{{ $user->profile->dietary_preference?->label() ?? __('pdf.nutrition_plan.not_specified') }}</strong></td>
                <td><span class="k">{{ __('pdf.nutrition_plan.diet_style') }}:</span> <strong>{{ $user->profile->diet_style?->label() ?? __('pdf.nutrition_plan.not_specified') }}</strong></td>
            </tr>
        </table>

        <table class="macro-row">
            <tr>
                <td class="macro macro--primary" width="25%">
                    <span class="macro__value">{{ $plan->daily_calories }}</span>
                    <span class="macro__label">{{ __('pdf.nutrition_plan.calories') }} (kcal)</span>
                </td>
                <td class="macro" width="25%">
                    <span class="macro__value">{{ $plan->daily_protein_g }}g</span>
                    <span class="macro__label">{{ __('pdf.nutrition_plan.protein') }}</span>
                </td>
                <td class="macro" width="25%">
                    <span class="macro__value">{{ $plan->daily_carbs_g }}g</span>
                    <span class="macro__label">{{ __('pdf.nutrition_plan.carbohydrates') }}</span>
                </td>
                <td class="macro" width="25%">
                    <span class="macro__value">{{ $plan->daily_fat_g }}g</span>
                    <span class="macro__label">{{ __('pdf.nutrition_plan.fat') }}</span>
                </td>
            </tr>
        </table>
    </div>

    @foreach($mealPlans as $mealPlan)
        <div class="day-container">
            <div class="day-heading">{{ __('pdf.nutrition_plan.day') }} {{ $mealPlan->day_number }} &middot; {{ $mealPlan->date->translatedFormat('l, M d, Y') }}</div>

            @if($mealPlan->status === 'generated' && $mealPlan->meals->count() > 0)
                @foreach($mealPlan->meals as $meal)
                    <table class="meal-card">
                        <thead class="meal-card__head">
                            <tr>
                                <th colspan="2"><span class="meal-card__type">{{ strtoupper(__("pdf.nutrition_plan.meal_type.$meal->type")) }}</span> &nbsp;&middot;&nbsp; {{ $meal->name }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="2" class="macro-strip">
                                <table>
                                    <tr>
                                        <td class="chip"><strong>{{ __('pdf.nutrition_plan.calories') }}:</strong> {{ $meal->calories }} kcal</td>
                                        <td class="chip"><strong>{{ __('pdf.nutrition_plan.protein') }}:</strong> {{ $meal->protein_g }}g</td>
                                        <td class="chip"><strong>{{ __('pdf.nutrition_plan.carbs') }}:</strong> {{ $meal->carbs_g }}g</td>
                                        <td class="chip"><strong>{{ __('pdf.nutrition_plan.fat') }}:</strong> {{ $meal->fat_g }}g</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        @if($meal->prep_time_minutes || $meal->cook_time_minutes)
                            <tr>
                                <td colspan="2" class="meta-line">
                                    @if($meal->prep_time_minutes)
                                        <strong>{{ __('pdf.nutrition_plan.prep_time') }}:</strong> {{ $meal->prep_time_minutes }} {{ __('pdf.nutrition_plan.min') }}
                                    @endif
                                    @if($meal->prep_time_minutes && $meal->cook_time_minutes) &nbsp;&middot;&nbsp; @endif
                                    @if($meal->cook_time_minutes)
                                        <strong>{{ __('pdf.nutrition_plan.cook_time') }}:</strong> {{ $meal->cook_time_minutes }} {{ __('pdf.nutrition_plan.min') }}
                                    @endif
                                </td>
                            </tr>
                        @endif

                        @if($meal->description)
                            <tr>
                                <td colspan="2" class="meal-desc">{{ $meal->description }}</td>
                            </tr>
                        @endif

                        @if($meal->ingredients && is_array($meal->ingredients) && count($meal->ingredients) > 0)
                            <tr>
                                <td colspan="2" class="section-cell nutrition-info">
                                    <span class="section-title">{{ __('pdf.nutrition_plan.ingredients') }}</span>
                                    <table class="list-table">
                                        @foreach($meal->ingredients as $ingredient)
                                            <tr>
                                                <td class="bullet">&bull;</td>
                                                <td>
                                                    @if(is_array($ingredient) || is_object($ingredient))
                                                        @php $ing = is_array($ingredient) ? $ingredient : (array) $ingredient; @endphp
                                                        @php
                                                            $unit = $ing['unit'] ?? null;
                                                            $amount = $ing['amount'] ?? null;
                                                            $isToTaste = $unit === 'to_taste';
                                                            $unitLabel = $unit ? __('units.'.$unit) : null;
                                                        @endphp
                                                        {{ $ing['name'] ?? 'Unknown' }}@if($isToTaste)<span class="amount"> &ndash; {{ $unitLabel }}</span>@elseif(filled($amount) && $unit)<span class="amount"> &ndash; {{ $amount }} {{ $unitLabel }}</span>@endif
                                                    @else
                                                        {{ $ingredient }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                        @endif

                        @if($meal->instructions && is_array($meal->instructions) && count($meal->instructions) > 0)
                            <tr>
                                <td colspan="2" class="section-cell">
                                    <span class="section-title">{{ __('pdf.nutrition_plan.instructions') }}</span>
                                    <table class="list-table">
                                        @foreach($meal->instructions as $index => $instruction)
                                            <tr>
                                                <td class="num">{{ $index + 1 }}.</td>
                                                <td>{{ is_string($instruction) ? $instruction : json_encode($instruction) }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                        @endif

                        @if($meal->allergens && is_array($meal->allergens) && count($meal->allergens) > 0)
                            <tr>
                                <td colspan="2" class="allergens">
                                    <strong>{{ __('pdf.nutrition_plan.allergens') }}:</strong>
                                    <span>{{ implode(', ', array_map(function ($item) { return is_string($item) ? ucfirst($item) : json_encode($item); }, $meal->allergens)) }}</span>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                @endforeach

                <div class="daily-totals">
                    <strong>{{ __('pdf.nutrition_plan.daily_totals') }}:</strong>
                    {{ $mealPlan->total_calories }} kcal &nbsp;&middot;&nbsp;
                    {{ $mealPlan->total_protein_g }}g {{ strtolower(__('pdf.nutrition_plan.protein')) }} &nbsp;&middot;&nbsp;
                    {{ $mealPlan->total_carbs_g }}g {{ strtolower(__('pdf.nutrition_plan.carbs')) }} &nbsp;&middot;&nbsp;
                    {{ $mealPlan->total_fat_g }}g {{ strtolower(__('pdf.nutrition_plan.fat')) }}
                </div>
            @else
                <p class="generating">{{ __('pdf.nutrition_plan.generating') }}</p>
            @endif
        </div>
    @endforeach

    <div class="help-box">
        <p><strong>{{ __('pdf.nutrition_plan.help') }}</strong></p>
        <p class="contact">{{ __('pdf.nutrition_plan.contact') }} <strong>hello@fytrr.com</strong></p>
    </div>
</main>

@include('pdf.partials.app_footer')

</body>
</html>
