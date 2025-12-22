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

        /* Markdown content styling */
        #content {
            font-size: 12px;
            line-height: 1.6;
            text-align: left;
            margin: 18px auto;
            color: #12181E;
        }

        /* Introduction paragraph */
        #content > p:first-of-type {
            font-size: 14px;
            margin-bottom: 25px;
        }

        /* Day headings (## Day) */
        #content h2 {
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #08233e;
            border-bottom: 1px solid #08233e;
            padding-bottom: 5px;
        }

        /* Meal headings (### Meal) */
        #content h3 {
            font-size: 15px;
            margin-top: 15px;
            margin-bottom: 10px;
            color: #333;
            font-weight: bold;
        }

        /* Styling for emojis in meal headings */
        #content h2 code, #content h3 code {
            background: none;
            font-family: inherit;
            padding: 0;
            font-size: 110%;
        }

        /* Lists */
        #content ul {
            margin: 5px 0 15px 0;
            padding-left: 30px;
        }

        #content li {
            margin-bottom: 3px;
        }

        /* Total calories styling - fixes the bullet point issue */
        #content p strong {
            display: block;
            background-color: #e6eef5;
            padding: 8px 15px;
            border-radius: 5px;
            margin: 15px 0;
            font-weight: bold;
        }

        /* Horizontal separators */
        #content hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #ccc;
        }

        /* Final summary paragraph */
        #content > p:last-of-type {
            margin-top: 20px;
            padding: 15px;
            background-color: #e6eef5;
            border-radius: 5px;
            font-size: 13px;
        }

        /* Page Break Control - Prevent awkward breaks */
        h2, h3, h4 {
            page-break-after: avoid;
            page-break-inside: avoid;
            orphans: 3;
            widows: 3;
        }

        .day-container {
            page-break-before: auto;
            page-break-after: auto;
        }

        /* Allow meal-card to break but keep header with some content */
        .meal-card {
            orphans: 4;
            widows: 4;
        }

        .meal-card h3 {
            page-break-after: avoid;
        }

        /* Keep nutrition info with heading */
        .nutrition-info {
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        /* Remove page-break-inside from sections as DomPDF doesn't handle it well with large lists */
        .ingredients-section h4,
        .instructions-section h4 {
            page-break-after: avoid;
            margin-top: 20px; /* More space when it's first on page */
        }

        /* Better orphan/widow control for lists */
        ul, ol {
            orphans: 3;
            widows: 3;
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

<header>
    <div id="logo">
        <img src="{{public_path('apple-touch-icon.png')}}" width="40" alt="fitness ai logo">

        <div>
            <h1>Meal Plan</h1>
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
        <h2 style="text-align: center; color: #08233e; margin-bottom: 30px;">Your Personalized {{ config('plans.duration_days', 28) }}-Day Meal Plan</h2>

        <div style="background-color: #e6eef5; padding: 15px; border-radius: 8px; margin-bottom: 30px;">
            <h3 style="margin-top: 0;">Your Daily Nutrition Targets</h3>
            <ul style="list-style: none; padding-left: 0;">
                <li><strong>Calories:</strong> {{ $plan->daily_calories }} kcal</li>
                <li><strong>Protein:</strong> {{ $plan->daily_protein_g }}g</li>
                <li><strong>Carbohydrates:</strong> {{ $plan->daily_carbs_g }}g</li>
                <li><strong>Fat:</strong> {{ $plan->daily_fat_g }}g</li>
            </ul>
        </div>

        @foreach($mealPlans as $mealPlan)
            <div class="day-container" style="page-break-inside: avoid; margin-bottom: 30px;">
                <h2>Day {{ $mealPlan->day_number }} - {{ $mealPlan->date->format('l, M d, Y') }}</h2>

                @if($mealPlan->status === 'generated' && $mealPlan->meals->count() > 0)
                    @foreach($mealPlan->meals as $meal)
                        <div class="meal-card" style="margin-bottom: 20px; padding: 15px; background-color: #f9f9f9; border-radius: 8px;">
                            <h3>{{ strtoupper($meal->type) }}: {{ $meal->name }}</h3>

                            <div class="nutrition-info" style="display: flex; gap: 20px; margin: 10px 0; font-size: 11px;">
                                <span><strong>Calories:</strong> {{ $meal->calories }} kcal</span>
                                <span><strong>Protein:</strong> {{ $meal->protein_g }}g</span>
                                <span><strong>Carbs:</strong> {{ $meal->carbs_g }}g</span>
                                <span><strong>Fat:</strong> {{ $meal->fat_g }}g</span>
                            </div>

                            @if($meal->prep_time_minutes || $meal->cook_time_minutes)
                                <div class="nutrition-info" style="margin: 10px 0; font-size: 11px; color: #666; padding: 8px; background-color: #fff; border-radius: 4px;">
                                    @if($meal->prep_time_minutes)
                                        <span><strong>Prep Time:</strong> {{ $meal->prep_time_minutes }} min</span>
                                    @endif
                                    @if($meal->cook_time_minutes)
                                        <span style="margin-left: 15px;"><strong>Cook Time:</strong> {{ $meal->cook_time_minutes }} min</span>
                                    @endif
                                </div>
                            @endif

                            @if($meal->description)
                                <p style="margin: 10px 0; font-style: italic; color: #555;">{{ $meal->description }}</p>
                            @endif

                            @if($meal->ingredients && is_array($meal->ingredients) && count($meal->ingredients) > 0)
                                <div class="ingredients-section">
                                    <h4 style="margin-top: 20px; margin-bottom: 8px; color: #08233e; page-break-after: avoid;">Ingredients:</h4>
                                    <ul style="margin: 0; padding-left: 25px; font-size: 11px;">
                                        @foreach($meal->ingredients as $ingredient)
                                            @if(is_array($ingredient) || is_object($ingredient))
                                                @php
                                                    $ing = is_array($ingredient) ? $ingredient : (array)$ingredient;
                                                @endphp
                                                <li style="page-break-inside: avoid;">
                                                    {{ $ing['name'] ?? 'Unknown' }}
                                                    @if(isset($ing['amount']) && isset($ing['unit']))
                                                        - {{ $ing['amount'] }}{{ $ing['unit'] }}
                                                    @endif
                                                </li>
                                            @else
                                                <li style="page-break-inside: avoid;">{{ $ingredient }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($meal->instructions && is_array($meal->instructions) && count($meal->instructions) > 0)
                                <div class="instructions-section">
                                    <h4 style="margin-top: 20px; margin-bottom: 8px; color: #08233e; page-break-after: avoid;">Instructions:</h4>
                                    <ol style="margin: 0; padding-left: 25px; font-size: 11px;">
                                        @foreach($meal->instructions as $instruction)
                                            <li style="margin-bottom: 5px; page-break-inside: avoid;">{{ is_string($instruction) ? $instruction : json_encode($instruction) }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endif

                            @if($meal->allergens && is_array($meal->allergens) && count($meal->allergens) > 0)
                                <div style="margin-top: 12px; padding: 8px 12px; background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                                    <strong style="color: #856404; font-size: 11px;">Allergens:</strong>
                                    <span style="color: #856404; font-size: 11px;">
                                        {{ implode(', ', array_map(function($item) { return is_string($item) ? ucfirst($item) : json_encode($item); }, $meal->allergens)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div style="background-color: #e6eef5; padding: 10px 15px; border-radius: 5px; margin-top: 15px;">
                        <strong>Daily Totals:</strong>
                        {{ $mealPlan->total_calories }} kcal |
                        {{ $mealPlan->total_protein_g }}g protein |
                        {{ $mealPlan->total_carbs_g }}g carbs |
                        {{ $mealPlan->total_fat_g }}g fat
                    </div>
                @else
                    <p style="color: #999; font-style: italic;">Meal plan for this day is being generated...</p>
                @endif

                <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
            </div>
        @endforeach

        <div style="margin-top: 40px; padding: 20px; background-color: #e6eef5; border-radius: 8px; text-align: center;">
            <p style="margin: 0; font-size: 14px;"><strong>Need help or have questions?</strong></p>
            <p style="margin: 5px 0 0 0;">Contact us at <strong>hello@fitnessai.me</strong></p>
        </div>
    </div>
</main>

</body>
</html>
