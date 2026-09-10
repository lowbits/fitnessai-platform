<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @include('pdf.partials.fonts_v2')

    @php
        $t = fn (string $key, array $r = []) => __('pdf.workout_plan_v2.'.$key, $r);
        $typeLabel = fn (?string $type) => \Illuminate\Support\Arr::get($t('types'), $type, ucfirst((string) $type));

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

        /* ---------- Running footer ---------- */
        .runner { position: fixed; left: 0; right: 0; }
        .footer { bottom: -34px; }
        .footer__page { float: right; }
        .footer__page:after { content: counter(page); }

        .footer td { font-size: 10.5px; color: #5c6b62; }
        .footer__brand { font-family: 'Space Grotesk', sans-serif; font-weight: 500; letter-spacing: 1px; color: #8a968f; }

        /* ---------- Per-page header (days 2+) ---------- */
        .page-head { margin-bottom: 26px; }
        .page-head td { font-size: 10.5px; color: #5c6b62; }
        .header__brand { font-family: 'Space Grotesk', sans-serif; font-weight: bold; color: #0c1310; }
        .header__plan { color: #5c6b62; }
        .header__brand, .header__plan { vertical-align: middle; }
        .header__right { text-align: right; }
        .header__rule { border-bottom: 1px solid #e6eae8; height: 10px; }

        .mark { width: 15px; height: 15px; vertical-align: -3px; }

        /* ---------- Page-1 cover ---------- */
        .cover td { vertical-align: middle; }
        .cover td.cover__logocell { width: 30px; vertical-align: top; }
        .cover__logo { width: 22px; height: 22px; margin-top: 4px; }
        .cover__brand {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: bold;
            font-size: 22px;
            line-height: 1;
            color: #0c1310;
        }
        .cover td.cover__date { text-align: right; font-size: 11px; color: #5c6b62; white-space: nowrap; vertical-align: top; }
        .cover__cal { width: 13px; height: 13px; vertical-align: -2px; margin-right: 3px; }
        .cover__sub { font-size: 12px; color: #5c6b62; margin-top: -3px; }
        .meta { width: auto; margin-top: 9px; }
        .meta td { padding-right: 26px; white-space: nowrap; }
        .meta__icon { width: 14px; height: 14px; vertical-align: -3px; margin-right: 6px; }
        .meta__text { font-size: 11px; color: #33403a; }
        .cover__rule { border-bottom: 1px solid #e6eae8; margin-top: 11px; }

        /* ---------- Day ---------- */
        .day { page-break-before: always; }
        .day.first { page-break-before: avoid; margin-top: 22px; }

        .eyebrow {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 500;
            font-size: 10px;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #17a45b;
        }

        .title {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: bold;
            font-size: 25px;
            color: #0c1310;
            margin: 3px 0 2px 0;
        }

        .muscles { font-size: 12px; color: #5c6b62; }

        .stats { width: auto; }
        .stats td { padding-left: 26px; text-align: left; }
        .stat__value { font-weight: bold; font-size: 12.5px; color: #0c1310; }
        .stat__label {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 8.5px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #8a968f;
        }

        .coach {
            border-left: 3px solid #17a45b;
            padding: 1px 0 1px 13px;
            margin: 12px 0 2px 0;
        }
        .coach__name {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 500;
            font-size: 9.5px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #17a45b;
        }
        .coach__text { font-size: 11px; color: #33403a; line-height: 1.25; margin-top: 1px; }

        /* ---------- Sections ---------- */
        .section { margin-top: 18px; }
        .section__title {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 500;
            font-size: 10.5px;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #17a45b;
            padding-bottom: 6px;
            border-bottom: 1px solid #e6eae8;
        }

        .plan { margin-top: 6px; }
        .section-head td {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 500;
            font-size: 10.5px;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #17a45b;
            padding: 22px 0 6px 0;
            border-bottom: 1px solid #e6eae8;
        }
        .section-head.first td { padding-top: 4px; }

        .col-head td {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 8.5px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #8a968f;
            padding: 7px 0 6px 0;
        }

        .row td {
            padding: 6px 8px 6px 0;
            border-bottom: 1px solid #eef1f0;
            font-size: 11.5px;
        }
        .num {
            width: 22px;
            text-align: right;
            padding-right: 10px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 500;
            color: #17a45b;
            font-size: 11.5px;
        }
        .ex__name { font-weight: bold; color: #0c1310; line-height: 1.1; }
        .ex__alt { font-size: 9.5px; color: #8a968f; line-height: 1.1; margin-top: -3px; }
        .cell-muted { color: #5c6b62; }
        .metric { font-weight: bold; color: #0c1310; }
        .time { color: #0c1310; font-weight: bold; }

        .box {
            border: 1px solid #d5dbd8;
            border-radius: 6px;
            height: 22px;
            width: 92%;
        }

        .set-hint-row td { font-size: 9px; color: #8a968f; text-align: right; padding: 4px 0 2px 0; }

        .check-cell { text-align: right; }
        .check {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 1px solid #cfd6d2;
            border-radius: 5px;
        }

        .notes .note-line { border-bottom: 1px solid #e6eae8; height: 26px; }
        .notes-page { page-break-before: always; }

        /* ---------- Rest-day app promo ---------- */
        .promo {
            margin-top: 30px;
            border: 1px solid #d8e6dd;
            border-radius: 16px;
            background: #f4faf6;
            table-layout: fixed;
        }
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
        .promo__btn {
            display: inline-block;
            background: #0c1310;
            color: #ffffff;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: bold;
            font-size: 13px;
            line-height: 1;
            padding: 13px 22px 12px 22px;
            border-radius: 10px;
            text-decoration: none;
        }
        .vordiv__line { width: 1px; height: 16px; background: #cdd9d2; margin: 0 auto; }
        .vordiv__txt {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 9px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #8a968f;
            padding: 5px 0;
        }
        .promo__phone img { width: 150px; }
    </style>
</head>
<body>

<div class="runner footer">
    <a class="footer__brand" href="{{ $appUrl }}" style="text-decoration:none;">FYTRR.COM</a>
    <span class="footer__page" style="font-family:'Space Grotesk',sans-serif; color:#8a968f;"></span>
</div>

@php
    $profile = $user->profile;
    $meta = array_filter([
        ['icon' => 'user', 'text' => $user->name],
        ['icon' => 'target', 'text' => $profile?->body_goal?->label()],
        ['icon' => 'map-pin', 'text' => $profile?->training_place?->label()],
        ['icon' => 'trending-up', 'text' => $profile?->skill_level?->label()],
        $plan->workouts_per_week
            ? ['icon' => 'repeat', 'text' => $plan->workouts_per_week.'× '.$t('sessions_per_week')]
            : null,
    ]);
    $firstDay = $workoutPlans->first()?->date;
    $lastDay = $workoutPlans->last()?->date;
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

@foreach ($workoutPlans as $workoutPlan)
    @php
        $warmups = $workoutPlan->warmupExercises();
        $cooldowns = $workoutPlan->cooldownExercises();
        $mains = $workoutPlan->mainExercises();
        $n = 0;

        // Notes only fill leftover space on the page the workout ends on; if the
        // workout fills the page, no notes are added and no extra page is created.
        $pageHeight = 1000;
        $used = ($loop->first ? 220 : 116)
            + ($workoutPlan->description ? 60 : 0)
            + ($warmups->isNotEmpty() ? 30 + $warmups->count() * 30 : 0)
            + ($mains->isNotEmpty() ? 80 + $mains->count() * 40 : 0)
            + ($cooldowns->isNotEmpty() ? 30 + $cooldowns->count() * 30 : 0);
        $pages = max(1, (int) ceil($used / $pageHeight));
        $noteLines = max(0, min(3, intdiv($pages * $pageHeight - $used - 120, 32)));
    @endphp

    <div class="day @if($loop->first) first @endif">
        @unless ($loop->first)
            <table class="page-head">
                <tr>
                    <td>
                        <img class="mark" src="{{ public_path('favicon.svg') }}" alt="">
                        <span class="header__brand">&nbsp;{{ $t('header_title') }}</span>
                        <span class="header__plan">&nbsp;&middot; {{ $plan->plan_name }}</span>
                    </td>
                    <td class="header__right">{{ $user->name }}</td>
                </tr>
                <tr><td colspan="2" class="header__rule"></td></tr>
            </table>
        @endunless
        @if ($workoutPlan->workout_type === 'rest')
            <div class="eyebrow">{{ $t('day') }} {{ $workoutPlan->day_number }} &middot; {{ $workoutPlan->date->translatedFormat('l, d.m.Y') }}</div>
            <div class="title">{{ $t('rest_day') }}</div>
            <div class="muscles">{{ $t('rest_description') }}</div>
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
        @else
        <table>
            <tr>
                <td style="width:58%;">
                    <div class="eyebrow">{{ $t('day') }} {{ $workoutPlan->day_number }} &middot; {{ $workoutPlan->date->translatedFormat('l, d.m.Y') }}</div>
                    <div class="title">{{ $workoutPlan->workout_name }}</div>
                    @if ($workoutPlan->muscle_groups && is_array($workoutPlan->muscle_groups))
                        <div class="muscles">{{ implode(', ', $workoutPlan->muscleGroupLabels()) }}</div>
                    @endif
                </td>
                <td style="width:42%; text-align:right;">
                    <table class="stats" align="right">
                        <tr>
                            <td>
                                <div class="stat__value">{{ $typeLabel($workoutPlan->workout_type) }}</div>
                                <div class="stat__label">{{ $t('type') }}</div>
                            </td>
                            @if ($workoutPlan->estimated_duration_minutes)
                                <td>
                                    <div class="stat__value">~{{ $workoutPlan->estimated_duration_minutes }} {{ $t('min') }}</div>
                                    <div class="stat__label">{{ $t('duration') }}</div>
                                </td>
                            @endif
                            <td>
                                <div class="stat__value">{{ $workoutPlan->exercises->count() }}</div>
                                <div class="stat__label">{{ $t('exercises') }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        @if ($workoutPlan->description)
            <div class="coach">
                <div class="coach__name">{{ $t('coach') }}</div>
                <div class="coach__text">{{ $workoutPlan->description }}</div>
            </div>
        @endif

        <table class="plan">
            @if ($warmups->isNotEmpty())
                <tr class="section-head first"><td colspan="8">{{ $t('warmup') }}</td></tr>
                @foreach ($warmups as $ex)
                    @php $n++; @endphp
                    <tr class="row">
                        <td class="num">{{ $n }}</td>
                        <td class="ex__name">{{ $ex->exercise?->localizedName() ?? $ex->name }}</td>
                        <td class="time">{{ $ex->durationLabel() }}</td>
                        <td></td><td></td><td></td><td></td>
                        <td class="check-cell"><span class="check"></span></td>
                    </tr>
                @endforeach
            @endif

            @if ($mains->isNotEmpty())
                <tr class="section-head @if($warmups->isEmpty()) first @endif"><td colspan="8">{{ $t('main') }}</td></tr>
                <tr class="col-head">
                    <td class="num">#</td>
                    <td>{{ $t('col_exercise') }}</td>
                    <td style="width:11%;">{{ $t('col_sets') }}</td>
                    <td style="width:10%;">{{ $t('col_rest') }}</td>
                    <td style="width:7%;">{{ $t('col_rpe') }}</td>
                    <td style="width:9%;">{{ $t('col_set') }} 1</td>
                    <td style="width:9%;">{{ $t('col_set') }} 2</td>
                    <td style="width:9%;">{{ $t('col_set') }} 3</td>
                </tr>
                @foreach ($mains as $ex)
                    @php $n++; $alt = $ex->alternativeNames(); @endphp
                    <tr class="row">
                        <td class="num">{{ $n }}</td>
                        <td>
                            <div class="ex__name">{{ $ex->exercise?->localizedName() ?? $ex->name }}</div>
                            @if ($alt !== '')
                                <div class="ex__alt">{{ $alt }}</div>
                            @endif
                        </td>
                        <td class="metric">{{ $ex->metricLabel() }}</td>
                        <td class="cell-muted">{{ $ex->rest_seconds }}@if($ex->rest_seconds) s @endif</td>
                        <td class="cell-muted">{{ $ex->rpe }}</td>
                        <td><div class="box"></div></td>
                        <td><div class="box"></div></td>
                        <td><div class="box"></div></td>
                    </tr>
                @endforeach
                <tr class="set-hint-row"><td colspan="8">{{ $t('set_hint') }}</td></tr>
            @endif

            @if ($cooldowns->isNotEmpty())
                <tr class="section-head @if($warmups->isEmpty() && $mains->isEmpty()) first @endif"><td colspan="8">{{ $t('cooldown') }}</td></tr>
                @foreach ($cooldowns as $ex)
                    @php $n++; @endphp
                    <tr class="row">
                        <td class="num">{{ $n }}</td>
                        <td class="ex__name">{{ $ex->exercise?->localizedName() ?? $ex->name }}</td>
                        <td class="time">{{ $ex->durationLabel() }}</td>
                        <td></td><td></td><td></td><td></td>
                        <td class="check-cell"><span class="check"></span></td>
                    </tr>
                @endforeach
            @endif
        </table>

        @if ($noteLines > 0)
            <div class="section notes">
                <div class="section__title">{{ $t('notes') }}</div>
                <table>
                    @for ($i = 0; $i < $noteLines; $i++)
                        <tr><td class="note-line"></td></tr>
                    @endfor
                </table>
            </div>
        @endif
        @endif
    </div>
@endforeach

</body>
</html>
