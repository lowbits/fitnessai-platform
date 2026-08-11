<?php

namespace App\Filament\Widgets;

use App\Enums\UserSource;
use App\Models\Exercise;
use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\User;
use App\Models\WorkoutPlan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

        $subscriptionChart = $this->getSubscriptionChartData();

        $failedWorkoutPlans = WorkoutPlan::where('status', 'failed')->count();
        $failedMealPlans = MealPlan::where('status', 'failed')->count();

        $pendingJobs = DB::table('jobs')->count();

        $recipesCount = Recipe::count();
        $exercisesCount = Exercise::count();

        $recipesChart = $this->getDailyCountsForLastDays(Recipe::query(), 'created_at', 30);
        $exercisesChart = $this->getDailyCountsForLastDays(Exercise::query(), 'created_at', 30);
        $newRecipesThisWeek = array_sum(array_slice($recipesChart, -7));
        $newExercisesThisWeek = array_sum(array_slice($exercisesChart, -7));

        $mobileUsersCount = User::mobile()->count();
        $convertedWebUsersCount = User::converted()->count();
        $totalWebUsers = User::where('source', UserSource::WEB)->count();
        $conversionRate = $totalWebUsers > 0
            ? round($convertedWebUsersCount / $totalWebUsers * 100, 1)
            : 0.0;

        $mobileChart = $this->getMobileUsersChartData();
        $convertedChart = $this->getConvertedUsersChartData();
        $newMobileThisWeek = array_sum(array_slice($mobileChart, -7));

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description("+{$newUsersThisWeek} this week")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($userChart)
                ->color('success'),

            Stat::make('Mobile Users', number_format($mobileUsersCount))
                ->description("+{$newMobileThisWeek} this week")
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->chart($mobileChart)
                ->color('info'),

            Stat::make('Web → Mobile', number_format($convertedWebUsersCount))
                ->description("{$conversionRate}% of web users converted")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($convertedChart)
                ->color('info'),

            Stat::make('Active Subscriptions', number_format($activeSubscriptions))
                ->chart($subscriptionChart)
                ->color('primary'),

            Stat::make('Recipes', number_format($recipesCount))
                ->description("+{$newRecipesThisWeek} this week")
                ->descriptionIcon('heroicon-m-book-open')
                ->chart($recipesChart)
                ->color('gray'),

            Stat::make('Exercises', number_format($exercisesCount))
                ->description("+{$newExercisesThisWeek} this week")
                ->descriptionIcon('heroicon-m-bolt')
                ->chart($exercisesChart)
                ->color('gray'),

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
        Builder $query,
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

        $nativeCounts = User::whereIn('source', UserSource::nativeMobileCases())
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)')
            ->pluck('count', 'date');

        $convertedCounts = $this->convertedCountsByDaySince($start);

        return collect(range(0, 29))
            ->map(fn (int $i) => ($nativeCounts[$start->copy()->addDays($i)->format('Y-m-d')] ?? 0)
                + ($convertedCounts[$start->copy()->addDays($i)->format('Y-m-d')] ?? 0))
            ->all();
    }

    /**
     * @return array<int, int>
     */
    private function getConvertedUsersChartData(): array
    {
        $start = now()->subDays(30)->startOfDay();
        $convertedCounts = $this->convertedCountsByDaySince($start);

        return collect(range(0, 29))
            ->map(fn (int $i) => $convertedCounts[$start->copy()->addDays($i)->format('Y-m-d')] ?? 0)
            ->all();
    }

    /**
     * Web-origin users bucketed by the day they first authenticated via mobile
     * (their earliest personal access token).
     *
     * @return Collection<string, int>
     */
    private function convertedCountsByDaySince(Carbon $start): Collection
    {
        return DB::table('personal_access_tokens')
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
