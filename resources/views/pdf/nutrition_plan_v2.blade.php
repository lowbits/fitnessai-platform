<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @include('pdf.partials.fonts_v2')

    @php
        $t = fn (string $key, array $r = []) => __('pdf.nutrition_plan_v2.'.$key, $r);
        $typeLabel = fn (?string $type) => \Illuminate\Support\Arr::get($t('meal_type'), $type, ucfirst((string) $type));

        $appUrl = \App\Support\AppDownloadQr::url($user);
        $appQr = \App\Support\AppDownloadQr::dataUri($appUrl, 6);
        $phonePath = public_path('assets/images/app/fytrr-app-home-'.(app()->getLocale() === 'de' ? 'de' : 'en').'.png');
    @endphp

    <style type="text/css">
        @page { margin: 50px 48px 58px 48px; }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Nunito', sans-serif;
            font-size: 11px;
            color: #0c1310;
            background: #ffffff;
        }

        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }

        /* ---------- Watermark (every page, bottom-right) ---------- */
        .watermark { position: fixed; right: -46px; bottom: -34px; width: 240px; height: 240px; }

        /* ---------- Running footer ---------- */
        .runner { position: fixed; left: 0; right: 0; }
        .footer { bottom: -34px; }
        .footer__page { float: right; }
        .footer__page:after { content: counter(page); }
        .footer td { font-size: 10.5px; color: #5c6b62; }
        .footer__brand { font-family: 'Space Grotesk', sans-serif; font-weight: 500; letter-spacing: 1px; color: #8a968f; }

        /* ---------- Page-1 cover ---------- */
        .cover td { vertical-align: middle; }
        .cover td.cover__logocell { width: 32px; vertical-align: top; }
        .cover__logo { width: 24px; height: 24px; margin-top: 3px; }
        .cover__brand { font-family: 'Space Grotesk', sans-serif; font-weight: bold; font-size: 22px; line-height: 1; color: #0c1310; }
        .cover td.cover__date { text-align: right; font-size: 11px; color: #5c6b62; white-space: nowrap; vertical-align: top; }
        .cover__cal { width: 13px; height: 13px; vertical-align: -2px; margin-right: 3px; }
        .cover__sub { font-size: 12px; color: #5c6b62; margin-top: -3px; }
        .meta { width: auto; margin-top: 9px; }
        .meta td { padding-right: 26px; white-space: nowrap; }
        .meta__icon { width: 14px; height: 14px; vertical-align: -3px; margin-right: 6px; }
        .meta__text { font-size: 11px; color: #33403a; }
        .cover__rule { border-bottom: 1px solid #e6eae8; margin-top: 11px; }

        /* ---------- Day ---------- */
        .day.first { margin-top: 22px; }
        .day-sep { border-top: 1px solid #e6eae8; margin-top: 30px; padding-top: 22px; }
        .day-head { page-break-after: avoid; }
        .eyebrow { font-family: 'Space Grotesk', sans-serif; font-weight: 500; font-size: 10px; letter-spacing: 1.4px; text-transform: uppercase; color: #17a45b; }
        .title { font-family: 'Space Grotesk', sans-serif; font-weight: bold; font-size: 25px; color: #0c1310; margin: 3px 0 2px 0; }
        .stats { width: auto; }
        .stats td { padding-left: 24px; text-align: left; }
        .stat__value { font-weight: bold; font-size: 12.5px; color: #0c1310; }
        .stat__label { font-family: 'Space Grotesk', sans-serif; font-size: 8.5px; letter-spacing: 1px; text-transform: uppercase; color: #8a968f; }

        /* ---------- Meal card ---------- */
        .meal { margin-top: 20px; }
        .meal__head { page-break-inside: avoid; }
        .meal__type { font-family: 'Space Grotesk', sans-serif; font-weight: 500; font-size: 10px; letter-spacing: 1.4px; text-transform: uppercase; color: #17a45b; }
        .meal__name { font-family: 'Space Grotesk', sans-serif; font-weight: bold; font-size: 15px; color: #0c1310; margin: 2px 0 0 0; }
        .macros { margin-top: 5px; font-size: 10.5px; color: #5c6b62; }
        .macros__kcal { font-weight: bold; color: #0c1310; }
        .macros__sep { color: #cdd9d2; }

        /* macro colors, matching the app (--v2-macro-*) */
        .m-protein { color: #288b8b; font-weight: bold; }
        .m-carbs { color: #b47e1c; font-weight: bold; }
        .m-fat { color: #c15f2d; font-weight: bold; }
        .meal__desc { font-size: 11px; color: #33403a; line-height: 1.3; margin-top: 7px; }

        .block { margin-top: 12px; }
        .block--ing { page-break-inside: avoid; }
        .block__title { font-family: 'Space Grotesk', sans-serif; font-weight: 500; font-size: 9.5px; letter-spacing: 1.4px; text-transform: uppercase; color: #17a45b; padding-bottom: 5px; border-bottom: 1px solid #e6eae8; margin-bottom: 7px; }

        /* two-column ingredient checklist */
        .ing td { width: 50%; padding: 2px 14px 2px 0; font-size: 11px; vertical-align: top; }
        .ing__box { display: inline-block; width: 11px; height: 11px; border: 1px solid #cfd6d2; border-radius: 3px; vertical-align: -1px; margin-right: 7px; }
        .ing__name { color: #0c1310; }
        .ing__detail { color: #8a968f; }

        /* numbered steps */
        .steps td { padding: 3px 0; font-size: 11px; vertical-align: top; line-height: 1.35; }
        .steps td.steps__num { width: 20px; font-family: 'Space Grotesk', sans-serif; font-weight: 500; color: #17a45b; }
        .steps__text { color: #33403a; }

        .allergens { margin-top: 9px; font-size: 10px; color: #8a968f; }
        .allergens strong { font-family: 'Space Grotesk', sans-serif; font-weight: 500; letter-spacing: 0.6px; text-transform: uppercase; color: #8a968f; }

        /* ---------- App promo ---------- */
        .promo { margin-top: 26px; border: 1px solid #d8e6dd; border-radius: 16px; background: #f4faf6; table-layout: fixed; page-break-inside: avoid; }
        .promo td { padding: 26px 0; vertical-align: middle; }
        .promo td.promo__text { width: 66.66%; padding-left: 30px; }
        .promo td.promo__phone { width: 33.34%; text-align: center; padding-right: 80px; }
        .promo__head { font-family: 'Space Grotesk', sans-serif; font-weight: bold; font-size: 23px; line-height: 1.1; color: #0c1310; }
        .promo__sub { font-size: 12.5px; color: #5c6b62; line-height: 1.3; margin: 2px 0 14px 0; }
        .promo__list { width: auto; margin: 0 0 20px 0; }
        .promo__list td { padding: 3px 0; vertical-align: middle; }
        .li__check { width: 13px; height: 13px; vertical-align: -2px; margin-right: 4px; }
        .promo__list td.li__t { font-size: 12px; color: #33403a; }
        .dl { width: auto; }
        .dl td { padding: 0; vertical-align: middle; }
        .dl td.vordiv { padding: 0 14px; text-align: center; }
        .promo__qrimg { width: 68px; height: 68px; border: 1px solid #e6eae8; border-radius: 8px; background: #ffffff; padding: 5px; vertical-align: middle; }
        .promo__btn { display: inline-block; background: #0c1310; color: #ffffff; font-family: 'Space Grotesk', sans-serif; font-weight: bold; font-size: 13px; line-height: 1; padding: 13px 22px 12px 22px; border-radius: 10px; text-decoration: none; }
        .vordiv__line { width: 1px; height: 16px; background: #cdd9d2; margin: 0 auto; }
        .vordiv__txt { font-family: 'Space Grotesk', sans-serif; font-size: 9px; letter-spacing: 0.5px; text-transform: uppercase; color: #8a968f; padding: 5px 0; }
        .promo__phone img { width: 150px; }
    </style>
</head>
<body>

<img class="watermark" src="{{ public_path('assets/images/watermark.png') }}" alt="">

<div class="runner footer">
    <a class="footer__brand" href="{{ $appUrl }}" style="text-decoration:none;">FYTRR.COM</a>
    <span class="footer__page" style="font-family:'Space Grotesk',sans-serif; color:#8a968f;"></span>
</div>

@php
    $profile = $user->profile;
    $meta = array_filter([
        ['icon' => 'user', 'text' => $user->name],
        ['icon' => 'target', 'text' => $profile?->body_goal?->label()],
        $plan->daily_calories ? ['icon' => 'flame', 'text' => number_format((int) $plan->daily_calories, 0, ',', '.').' '.$t('kcal_per_day')] : null,
        $plan->duration_days ? ['icon' => 'calendar', 'text' => $t('days', ['count' => $plan->duration_days])] : null,
    ]);
    $firstDay = $mealPlans->first()?->date;
    $lastDay = $mealPlans->last()?->date;
@endphp

<table class="cover">
    <tr>
        <td class="cover__logocell"><img class="cover__logo" src="{{ public_path('favicon.svg') }}" alt=""></td>
        <td>
            <div class="cover__brand">{{ $t('header_title') }}</div>
            @if ($plan->plan_name)
                <div class="cover__sub">{{ $plan->plan_name }}</div>
            @endif
        </td>
        @if ($firstDay && $lastDay)
            <td class="cover__date">
                <img class="cover__cal" src="{{ public_path('assets/icons/calendar.svg') }}" alt="">
                {{ $firstDay->translatedFormat('d.m.') }} &ndash; {{ $lastDay->translatedFormat('d.m.Y') }}
            </td>
        @endif
    </tr>
</table>
<table class="meta">
    <tr>
        @foreach ($meta as $item)
            <td>
                <img class="meta__icon" src="{{ public_path('assets/icons/'.$item['icon'].'.svg') }}" alt="">
                <span class="meta__text">{{ $item['text'] }}</span>
            </td>
        @endforeach
    </tr>
</table>
<div class="cover__rule"></div>

@foreach ($mealPlans as $mealPlan)
    @php $showPromo = $loop->last || $loop->iteration % 2 === 0; @endphp
    <div class="day @if($loop->first) first @endif">
        @unless ($loop->first)
            <div class="day-sep"></div>
        @endunless

        <table class="day-head">
            <tr>
                <td style="width:52%;">
                    <div class="eyebrow">{{ $mealPlan->date->translatedFormat('l, d.m.Y') }}</div>
                    <div class="title">{{ $t('day') }} {{ $mealPlan->day_number }}</div>
                </td>
                <td style="width:48%; text-align:right;">
                    <table class="stats" align="right">
                        <tr>
                            <td><div class="stat__value">{{ number_format((int) $mealPlan->total_calories, 0, ',', '.') }}</div><div class="stat__label">{{ $t('calories') }}</div></td>
                            <td><div class="stat__value m-protein">{{ (int) $mealPlan->total_protein_g }} g</div><div class="stat__label">{{ $t('protein') }}</div></td>
                            <td><div class="stat__value m-carbs">{{ (int) $mealPlan->total_carbs_g }} g</div><div class="stat__label">{{ $t('carbs') }}</div></td>
                            <td><div class="stat__value m-fat">{{ (int) $mealPlan->total_fat_g }} g</div><div class="stat__label">{{ $t('fat') }}</div></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        @foreach ($mealPlan->meals as $meal)
            @php $ingredients = $meal->formattedIngredients(); @endphp
            <div class="meal">
                <div class="meal__head">
                    <div class="meal__type">{{ $typeLabel($meal->type) }}</div>
                    <div class="meal__name">{{ $meal->name }}</div>
                    <div class="macros">
                        <span class="macros__kcal">{{ (int) $meal->calories }} {{ $t('calories') }}</span>
                        <span class="macros__sep">&nbsp;&middot;&nbsp;</span><span class="m-protein">{{ (int) $meal->protein_g }} g {{ $t('protein') }}</span>
                        <span class="macros__sep">&nbsp;&middot;&nbsp;</span><span class="m-carbs">{{ (int) $meal->carbs_g }} g {{ $t('carbs') }}</span>
                        <span class="macros__sep">&nbsp;&middot;&nbsp;</span><span class="m-fat">{{ (int) $meal->fat_g }} g {{ $t('fat') }}</span>
                        @if ($meal->totalTimeMinutes() > 0)
                            <span class="macros__sep">&nbsp;&middot;&nbsp;</span>{{ $meal->totalTimeMinutes() }} {{ $t('min') }}
                        @endif
                    </div>
                    @if ($meal->description)
                        <div class="meal__desc">{{ $meal->description }}</div>
                    @endif
                </div>

                @if (count($ingredients) > 0)
                    <div class="block block--ing">
                        <div class="block__title">{{ $t('ingredients') }}</div>
                        <table class="ing">
                            @foreach (array_chunk($ingredients, 2) as $pair)
                                <tr>
                                    @foreach ($pair as $ing)
                                        <td>
                                            <span class="ing__box"></span><span class="ing__name">{{ $ing['name'] }}</span>@if ($ing['detail'] !== '') <span class="ing__detail">&middot; {{ $ing['detail'] }}</span>@endif
                                        </td>
                                    @endforeach
                                    @if (count($pair) === 1)<td></td>@endif
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif

                @if (is_array($meal->instructions) && count($meal->instructions) > 0)
                    <div class="block">
                        <div class="block__title">{{ $t('instructions') }}</div>
                        <table class="steps">
                            @foreach ($meal->instructions as $i => $step)
                                <tr>
                                    <td class="steps__num">{{ $i + 1 }}</td>
                                    <td class="steps__text">{{ is_string($step) ? $step : '' }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif

                @if (is_array($meal->allergens) && count($meal->allergens) > 0)
                    <div class="allergens"><strong>{{ $t('allergens') }}:</strong> {{ implode(', ', array_map('ucfirst', $meal->allergens)) }}</div>
                @endif
            </div>
        @endforeach

        @if ($showPromo)
            <table class="promo">
                <tr>
                    <td class="promo__text">
                        <div class="promo__head">{{ $t('promo_head') }}</div>
                        <div class="promo__sub">{{ $t('promo_sub') }}</div>
                        <table class="promo__list">
                            @foreach ($t('promo_list') as $item)
                                <tr>
                                    <td class="li__t"><img class="li__check" src="{{ public_path('assets/icons/check.svg') }}" alt="">{{ $item }}</td>
                                </tr>
                            @endforeach
                        </table>
                        <table class="dl">
                            <tr>
                                <td><img class="promo__qrimg" src="{{ $appQr }}" alt=""></td>
                                <td class="vordiv">
                                    <div class="vordiv__line"></div>
                                    <div class="vordiv__txt">{{ $t('promo_or') }}</div>
                                    <div class="vordiv__line"></div>
                                </td>
                                <td><a class="promo__btn" href="{{ $appUrl }}">{{ $t('promo_cta') }}</a></td>
                            </tr>
                        </table>
                    </td>
                    <td class="promo__phone"><img src="{{ $phonePath }}" alt=""></td>
                </tr>
            </table>
        @endif
    </div>
@endforeach

</body>
</html>
