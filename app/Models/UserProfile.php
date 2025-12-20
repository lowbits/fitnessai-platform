<?php

namespace App\Models;

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\DietType;
use App\Enums\Gender;
use App\Enums\SkillLevel;
use App\Enums\TrainingPlace;
use App\Helpers\Metabolism;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'age',
        'gender',
        'weight_kg',
        'height_cm',
        'body_goal',
        'skill_level',
        'activity_level',
        'training_place',
        'diet_type',
        'training_sessions_per_week',
    ];

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
            'body_goal' => BodyGoal::class,
            'skill_level' => SkillLevel::class,
            'activity_level' => ActivityLevel::class,
            'training_place' => TrainingPlace::class,
            'diet_type' => DietType::class,
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
            $this->weight_kg
        );
    }

    /**
     * Calculate Total Daily Energy Expenditure.
     */
    public function calculateTDEE(): int
    {
        $bmr = $this->calculateBMR();
        return Metabolism::calculateTDEE($bmr, $this->activity_level);
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
     *
     * @return array{protein_g: int, carbs_g: int, fat_g: int}
     */
    public function calculateMacros(): array
    {
        $dailyCalories = $this->calculateDailyCalories();
        return Metabolism::calculateMacros($dailyCalories, $this->diet_type);
    }

    /**
     * Get complete metabolism data.
     */
    public function getMetabolismData(): array
    {
        $bmr = $this->calculateBMR();
        $tdee = $this->calculateTDEE();
        $dailyCalories = $this->calculateDailyCalories();
        $macros = $this->calculateMacros();

        return [
            'bmr' => $bmr,
            'tdee' => $tdee,
            'daily_calories' => $dailyCalories,
            'protein_g' => $macros['protein_g'],
            'carbs_g' => $macros['carbs_g'],
            'fat_g' => $macros['fat_g'],
            'goal_adjustment' => $this->body_goal->calorieAdjustment(),
            'activity_multiplier' => $this->activity_level->tdeeMultiplier(),
            'macro_split' => $this->diet_type->macroSplit(),
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}
