<?php

namespace App\Filament\Widgets;

use App\Models\MealPlan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $newUsersThisWeek = User::where('created_at', '>=', now()->subWeek())->count();

        $userChart = $this->getDailyCountsForLastDays(
            User::query(),
            'created_at',
            30,
        );

        $activeSubscriptions = DB::table('subscriptions')
            ->where('status', 'active')
            ->count();

        $activeLegacySubscriptions = DB::table('subscriptions_legacy')
            ->where('status', 'active')
            ->whereDate('ends_at', '>=', now())
            ->count();

        $subscriptionChart = $this->getSubscriptionChartData();

        $failedWorkoutPlans = WorkoutPlan::where('status', 'failed')->count();
        $failedMealPlans = MealPlan::where('status', 'failed')->count();

        $pendingJobs = DB::table('jobs')->count();

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description("+{$newUsersThisWeek} this week")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($userChart)
                ->color('success'),

            Stat::make('Active Subscriptions', number_format($activeSubscriptions + $activeLegacySubscriptions))
                ->description("RC: {$activeSubscriptions} / Legacy: {$activeLegacySubscriptions}")
                ->chart($subscriptionChart)
                ->color('primary'),

            Stat::make('Failed Workouts', number_format($failedWorkoutPlans))
                ->color($failedWorkoutPlans > 0 ? 'danger' : 'success'),

            Stat::make('Failed Meals', number_format($failedMealPlans))
                ->color($failedMealPlans > 0 ? 'danger' : 'success'),

            Stat::make('Pending Jobs', number_format($pendingJobs))
                ->color($pendingJobs > 10 ? 'warning' : 'success'),
        ];
    }

    /**
     * @return array<int, int>
     */
    private function getDailyCountsForLastDays(
        \Illuminate\Database\Eloquent\Builder $query,
        string $dateColumn,
        int $days,
    ): array {
        $start = now()->subDays($days)->startOfDay();

        $counts = $query
            ->where($dateColumn, '>=', $start)
            ->selectRaw("DATE({$dateColumn}) as date, COUNT(*) as count")
            ->groupByRaw("DATE({$dateColumn})")
            ->pluck('count', 'date');

        return collect(range(0, $days - 1))
            ->map(fn (int $i) => $counts[$start->copy()->addDays($i)->format('Y-m-d')] ?? 0)
            ->all();
    }

    /**
     * @return array<int, int>
     */
    private function getSubscriptionChartData(): array
    {
        $start = now()->subDays(30)->startOfDay();

        $counts = DB::table('subscriptions')
            ->where('status', 'active')
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)')
            ->pluck('count', 'date');

        return collect(range(0, 29))
            ->map(fn (int $i) => $counts[$start->copy()->addDays($i)->format('Y-m-d')] ?? 0)
            ->all();
    }
}
