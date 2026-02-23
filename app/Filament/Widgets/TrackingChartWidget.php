<?php

namespace App\Filament\Widgets;

use App\Models\BodyProgress;
use App\Models\CalorieTracking;
use App\Models\WorkoutTracking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TrackingChartWidget extends ChartWidget
{
    protected ?string $heading = 'Daily Tracking Activity';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last 7 days',
            'month' => 'Last 30 days',
            '3months' => 'Last 90 days',
        ];
    }

    protected function getData(): array
    {
        $days = match ($this->filter) {
            'week' => 7,
            '3months' => 90,
            default => 30,
        };

        $start = now()->subDays($days)->startOfDay();
        $dates = collect(range(0, $days - 1))
            ->map(fn (int $i) => $start->copy()->addDays($i)->format('Y-m-d'));

        $workouts = WorkoutTracking::query()
            ->where('completed_at', '>=', $start)
            ->selectRaw('DATE(completed_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(completed_at)')
            ->pluck('count', 'date');

        $meals = CalorieTracking::query()
            ->where('tracked_date', '>=', $start)
            ->selectRaw('tracked_date as date, COUNT(*) as count')
            ->groupBy('tracked_date')
            ->pluck('count', 'date');

        $bodyProgress = BodyProgress::query()
            ->where('recorded_at', '>=', $start)
            ->selectRaw('DATE(recorded_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(recorded_at)')
            ->pluck('count', 'date');

        return [
            'datasets' => [
                [
                    'label' => 'Workouts',
                    'data' => $dates->map(fn (string $date) => $workouts[$date] ?? 0)->values()->all(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Meals Tracked',
                    'data' => $dates->map(fn (string $date) => $meals[$date] ?? 0)->values()->all(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Body Progress',
                    'data' => $dates->map(fn (string $date) => $bodyProgress[$date] ?? 0)->values()->all(),
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $dates->map(fn (string $date) => Carbon::parse($date)->format('M j'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
