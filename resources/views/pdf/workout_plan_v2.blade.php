<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @include('pdf.partials.fonts_v2')

    @php
        $t = fn (string $key, array $r = []) => __('pdf.workout_plan_v2.'.$key, $r);
        $typeLabel = fn (?string $type) => \Illuminate\Support\Arr::get($t('types'), $type, ucfirst((string) $type));
        $duration = function (?int $seconds): string {
            $seconds = (int) $seconds;
            if ($seconds <= 0) {
                return '';
            }
            $minutes = intdiv($seconds, 60);
            $rest = $seconds % 60;
            return match (true) {
                $minutes === 0 => $rest.'s',
                $rest === 0 => $minutes.'min',
                default => $minutes.'min '.$rest.'s',
            };
        };
        $altNames = function ($alternatives): string {
            if (! is_array($alternatives)) {
                return '';
            }
            return collect($alternatives)
                ->map(fn ($alt) => is_string($alt) ? $alt : ($alt['name'] ?? null))
                ->filter()
                ->take(2)
                ->join(', ');
        };
        $metric = function ($ex) use ($duration): string {
            if ($ex->reps) {
                return $ex->sets.' × '.$ex->reps;
            }
            if ($ex->duration_seconds) {
                return $ex->sets ? $ex->sets.' × '.$duration($ex->duration_seconds) : $duration($ex->duration_seconds);
            }
            return (string) $ex->sets;
        };
        $cols = '<colgroup><col style="width:26px"><col><col style="width:13%"><col style="width:10%"><col style="width:7%"><col style="width:12%"><col style="width:12%"><col style="width:12%"></colgroup>';
    @endphp

    <style type="text/css">
        @page { margin: 96px 48px 58px 48px; }

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

        /* ---------- Running header / footer ---------- */
        .runner { position: fixed; left: 0; right: 0; }
        .header { top: -66px; }
        .footer { bottom: -34px; }
        .footer__page { float: right; }
        .footer__page:after { content: counter(page); }

        .header td, .footer td { font-size: 10.5px; color: #5c6b62; }
        .header__brand { font-family: 'Space Grotesk', sans-serif; font-weight: bold; color: #0c1310; }
        .header__plan { color: #5c6b62; }
        .header__right, .footer__right { text-align: right; }
        .header__rule { border-bottom: 1px solid #e6eae8; height: 10px; }
        .footer__brand { font-family: 'Space Grotesk', sans-serif; font-weight: 500; letter-spacing: 1px; color: #8a968f; }

        .mark { width: 16px; height: 16px; }

        /* ---------- Day ---------- */
        .day { page-break-before: always; }
        .day.first { page-break-before: avoid; }

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
        .coach__text { font-size: 11px; color: #33403a; line-height: 1.35; margin-top: 2px; }

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
            width: 26px;
            text-align: right;
            padding-right: 12px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 500;
            color: #17a45b;
            font-size: 11.5px;
        }
        .ex__name { font-weight: bold; color: #0c1310; }
        .ex__alt { font-size: 9.5px; color: #8a968f; padding-top: 2px; }
        .cell-muted { color: #5c6b62; }
        .time { color: #33403a; }

        .box {
            border: 1px solid #d5dbd8;
            border-radius: 6px;
            height: 22px;
            width: 92%;
        }

        .set-hint { font-size: 9px; color: #8a968f; text-align: right; padding-top: 6px; }

        .notes .note-line { border-bottom: 1px solid #e6eae8; height: 26px; }
    </style>
</head>
<body>

<table class="runner header">
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

<div class="runner footer">
    <span class="footer__brand">FYTRR.COM</span>
    <span class="footer__page" style="font-family:'Space Grotesk',sans-serif; color:#8a968f;"></span>
</div>

@foreach ($workoutPlans as $workoutPlan)
    @php
        $warmups = $workoutPlan->exercises->where('type', 'warmup')->values();
        $cooldowns = $workoutPlan->exercises->whereIn('type', ['cooldown', 'stretch'])->values();
        $mains = $workoutPlan->exercises->whereNotIn('type', ['warmup', 'cooldown', 'stretch'])->values();
        $n = 0;
    @endphp

    <div class="day @if($loop->first) first @endif">
        <table>
            <tr>
                <td style="width:58%;">
                    <div class="eyebrow">{{ $t('day') }} {{ $workoutPlan->day_number }} &middot; {{ $workoutPlan->date->translatedFormat('l, d.m.Y') }}</div>
                    <div class="title">{{ $workoutPlan->workout_name }}</div>
                    @if ($workoutPlan->muscle_groups && is_array($workoutPlan->muscle_groups))
                        <div class="muscles">{{ implode(', ', array_map('ucfirst', $workoutPlan->muscle_groups)) }}</div>
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

        @if ($warmups->isNotEmpty())
            <div class="section">
                <div class="section__title">{{ $t('warmup') }}</div>
                <table>{!! $cols !!}
                    @foreach ($warmups as $ex)
                        @php $n++; @endphp
                        <tr class="row">
                            <td class="num">{{ $n }}</td>
                            <td class="ex__name">{{ $ex->exercise?->localizedName() ?? $ex->name }}</td>
                            <td class="time">{{ $duration($ex->duration_seconds) }}</td>
                            <td></td><td></td><td></td><td></td><td></td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        @if ($mains->isNotEmpty())
            <div class="section">
                <div class="section__title">{{ $t('main') }}</div>
                <table>{!! $cols !!}
                    <tr class="col-head">
                        <td class="num">#</td>
                        <td>{{ $t('col_exercise') }}</td>
                        <td>{{ $t('col_sets') }}</td>
                        <td>{{ $t('col_rest') }}</td>
                        <td>{{ $t('col_rpe') }}</td>
                        <td>{{ $t('col_set') }} 1</td>
                        <td>{{ $t('col_set') }} 2</td>
                        <td>{{ $t('col_set') }} 3</td>
                    </tr>
                    @foreach ($mains as $ex)
                        @php $n++; $alt = $altNames($ex->alternatives); @endphp
                        <tr class="row">
                            <td class="num">{{ $n }}</td>
                            <td>
                                <div class="ex__name">{{ $ex->exercise?->localizedName() ?? $ex->name }}</div>
                                @if ($alt !== '')
                                    <div class="ex__alt">{{ $alt }}</div>
                                @endif
                            </td>
                            <td class="cell-muted">{{ $metric($ex) }}</td>
                            <td class="cell-muted">{{ $ex->rest_seconds }}@if($ex->rest_seconds) s @endif</td>
                            <td class="cell-muted">{{ $ex->rpe }}</td>
                            <td><div class="box"></div></td>
                            <td><div class="box"></div></td>
                            <td><div class="box"></div></td>
                        </tr>
                    @endforeach
                </table>
                <div class="set-hint">{{ $t('set_hint') }}</div>
            </div>
        @endif

        @if ($cooldowns->isNotEmpty())
            <div class="section">
                <div class="section__title">{{ $t('cooldown') }}</div>
                <table>{!! $cols !!}
                    @foreach ($cooldowns as $ex)
                        @php $n++; @endphp
                        <tr class="row">
                            <td class="num">{{ $n }}</td>
                            <td class="ex__name">{{ $ex->exercise?->localizedName() ?? $ex->name }}</td>
                            <td class="time">{{ $duration($ex->duration_seconds) }}</td>
                            <td></td><td></td><td></td><td></td><td></td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        <div class="section notes">
            <div class="section__title">{{ $t('notes') }}</div>
            <table>
                <tr><td class="note-line"></td></tr>
                <tr><td class="note-line"></td></tr>
                <tr><td class="note-line"></td></tr>
            </table>
        </div>
    </div>
@endforeach

</body>
</html>
