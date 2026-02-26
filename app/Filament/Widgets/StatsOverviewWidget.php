<?php

namespace App\Filament\Widgets;

use App\Enums\UserSource;
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

        $convertedWebUsersCount = User::where('source', UserSource::WEB)->whereHas('tokens')->count();

        $mobileUsersCount = User::where('source', UserSource::MOBILE_APPLE)->count()
            + $convertedWebUsersCount;

        $mobileChart = $this->getMobileUsersChartData();

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description("+{$newUsersThisWeek} this week")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($userChart)
                ->color('success'),

            Stat::make('Mobile Users', number_format($mobileUsersCount))
                ->description("{$convertedWebUsersCount} converted from web")
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->chart($mobileChart)
                ->color('info'),

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
    private function getMobileUsersChartData(): array
    {
        $start = now()->subDays(30)->startOfDay();

        // Native mobile sign-ups per day
        $nativeCounts = User::where('source', UserSource::MOBILE_APPLE)
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)')
            ->pluck('count', 'date');

        // Web users who first logged in via mobile (first token created) per day
        $convertedCounts = DB::table('personal_access_tokens')
            ->join('users', function ($join) {
                $join->on('personal_access_tokens.tokenable_id', '=', 'users.id')
                    ->where('personal_access_tokens.tokenable_type', '=', (new User)->getMorphClass())
                    ->where('users.source', '=', UserSource::WEB->value);
            })
            ->where('personal_access_tokens.created_at', '>=', $start)
            ->whereRaw('personal_access_tokens.id = (SELECT MIN(pat2.id) FROM personal_access_tokens pat2 WHERE pat2.tokenable_id = personal_access_tokens.tokenable_id AND pat2.tokenable_type = personal_access_tokens.tokenable_type)')
            ->selectRaw('DATE(personal_access_tokens.created_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(personal_access_tokens.created_at)')
            ->pluck('count', 'date');

        return collect(range(0, 29))
            ->map(fn (int $i) => ($nativeCounts[$start->copy()->addDays($i)->format('Y-m-d')] ?? 0)
                + ($convertedCounts[$start->copy()->addDays($i)->format('Y-m-d')] ?? 0))
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
