<?php

namespace App\Models;

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\CookingFrequency;
use App\Enums\CookingPreference;
use App\Enums\DietaryPreference;
use App\Enums\DietStyle;
use App\Enums\DietType;
use App\Enums\Gender;
use App\Enums\MealVariety;
use App\Enums\SkillLevel;
use App\Enums\TrainingPlace;
use App\Helpers\Metabolism;
use App\ValueObjects\MacroDistribution;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'birthdate',
        'gender',
        'weight_kg',
        'height_cm',
        'body_goal',
        'skill_level',
        'activity_level',
        'training_place',
        'diet_type',
        'dietary_preference',
        'diet_style',
        'training_sessions_per_week',
        'training_days',
        'selected_meals',
        'food_dislikes',
        'disliked_recipe_ids',
        'cooking_preference',
        'cooking_frequency',
        'meal_variety',
        'physical_limitations',
        'physical_limitations_note',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date:Y-m-d',
            'gender' => Gender::class,
            'body_goal' => BodyGoal::class,
            'skill_level' => SkillLevel::class,
            'activity_level' => ActivityLevel::class,
            'training_place' => TrainingPlace::class,
            'diet_type' => DietType::class,
            'dietary_preference' => DietaryPreference::class,
            'diet_style' => DietStyle::class,
            'training_days' => 'array',
            'selected_meals' => 'array',
            'food_dislikes' => 'array',
            'disliked_recipe_ids' => 'array',
            'cooking_preference' => CookingPreference::class,
            'cooking_frequency' => CookingFrequency::class,
            'meal_variety' => MealVariety::class,
            'physical_limitations' => 'array',
        ];
    }

    /**
     * Calculate Basal Metabolic Rate.
     */
    public function calculateBMR(): int
    {
        return Metabolism::calculateBMR(
            $this->gender,
            $this->age,
            $this->height_cm,
            $this->user->getCurrentWeight()
        );
    }

    /**
     * Calculate Total Daily Energy Expenditure.
     */
    public function calculateTDEE(): int
    {
        $bmr = $this->calculateBMR();

        return Metabolism::calculateTDEE(
            $bmr,
            $this->activity_level,
            $this->training_sessions_per_week,
            $this->user->getCurrentWeight()
        );
    }

    /**
     * Calculate daily calorie target.
     */
    public function calculateDailyCalories(): int
    {
        $tdee = $this->calculateTDEE();

        return Metabolism::calculateDailyCalories($tdee, $this->body_goal);
    }

    /**
     * Calculate macro split in grams.
     */
    public function calculateMacros(): MacroDistribution
    {
        return Metabolism::calculateMacros(
            dailyCalories: $this->calculateDailyCalories(),
            bodyWeight: $this->user->getCurrentWeight(),
            goal: $this->body_goal,
            carbFatRatio: $this->resolveCarbFatRatio(),
            gender: $this->gender,
            age: $this->age,
            height: $this->height_cm,
        );
    }

    /**
     * Get complete metabolism data.
     *
     * @return array{
     *     bmr: int,
     *     tdee: int,
     *     daily_calories: int,
     *     activity_multiplier: float,
     *     training_sessions_per_week: int,
     *     goal: string,
     *     goal_adjustment: int,
     *     diet: string,
     *     carb_fat_ratio: array{carbs: int, fat: int},
     *     protein_g: int,
     *     carbs_g: int,
     *     fat_g: int,
     *     protein_challenging: bool,
     *     minimum_fat_enforced: bool,
     * }
     */
    public function getMetabolismData(): array
    {
        $macros = $this->calculateMacros();
        $preference = $this->resolveDietaryPreference();

        return [
            'bmr' => $this->calculateBMR(),
            'tdee' => $this->calculateTDEE(),
            'daily_calories' => $this->calculateDailyCalories(),
            'activity_multiplier' => $this->activity_level->tdeeMultiplier(),
            'training_sessions_per_week' => $this->training_sessions_per_week,
            'goal' => $this->body_goal->value,
            'goal_adjustment' => $this->body_goal->calorieAdjustment(),
            'dietary_preference' => $preference->value,
            'diet_style' => $this->diet_style?->value,
            'carb_fat_ratio' => $this->resolveCarbFatRatio(),
            ...$macros->toArray(),
        ];
    }

    /**
     * Resolve what the user eats.
     * Used for meal plan food selection and excluded ingredients.
     *
     * Priority: dietary_preference > diet_type (legacy) > omnivore fallback.
     */
    public function resolveDietaryPreference(): DietaryPreference|DietType
    {
        return $this->dietary_preference
            ?? $this->diet_type
            ?? DietaryPreference::OMNIVORE;
    }

    /**
     * Resolve how macros are distributed (carb/fat ratio).
     * Used for macro calculations.
     *
     * Priority: diet_style (new) > dietary_preference > diet_type (legacy).
     *
     * @return array{carbs: int, fat: int}
     */
    public function resolveCarbFatRatio(): array
    {
        return $this->diet_style?->carbFatRatio()
            ?? $this->resolveDietaryPreference()->carbFatRatio();
    }

    public function getDietaryInfo(): string
    {
        return filled($this->dietary_preference?->value)
            ? $this->dietary_preference->value
            : ($this->diet_type?->value ?? '-');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birthdate?->age;
    }
}
