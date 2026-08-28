<?php

namespace App\Http\Requests\Api\V3;

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\CookingFrequency;
use App\Enums\CookingPreference;
use App\Enums\DietaryPreference;
use App\Enums\DietStyle;
use App\Enums\Gender;
use App\Enums\MealVariety;
use App\Enums\SkillLevel;
use App\Enums\TrainingPlace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateProfileRequest extends FormRequest
{
    private const USER_FIELDS = ['name', 'locale'];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'locale' => ['sometimes', 'string', 'in:en,de'],

            'birthdate' => ['sometimes', 'date', 'before:-13 years', 'after:-120 years'],
            'gender' => ['sometimes', new Enum(Gender::class)],
            'height_cm' => ['sometimes', 'numeric', 'min:100', 'max:250'],
            'goal_weight_kg' => ['sometimes', 'nullable', 'numeric', 'min:30', 'max:300'],
            'body_goal' => ['sometimes', Rule::in(array_map(fn ($g) => $g->value, BodyGoal::current()))],
            'skill_level' => ['sometimes', new Enum(SkillLevel::class)],
            'activity_level' => ['sometimes', new Enum(ActivityLevel::class)],
            'training_place' => ['sometimes', new Enum(TrainingPlace::class)],
            'training_sessions_per_week' => ['sometimes', 'integer', 'min:1', 'max:7'],
            'training_days' => ['sometimes', 'array'],

            'dietary_preference' => ['sometimes', new Enum(DietaryPreference::class)],
            'diet_style' => ['sometimes', 'nullable', new Enum(DietStyle::class)],

            'selected_meals' => ['sometimes', 'array'],
            'selected_meals.*' => ['string', 'in:breakfast,lunch,snack,dinner'],
            'food_dislikes' => ['sometimes', 'array'],
            'food_dislikes.*' => ['string', 'max:100'],
            'cooking_preference' => ['sometimes', new Enum(CookingPreference::class)],
            'cooking_frequency' => ['sometimes', new Enum(CookingFrequency::class)],
            'meal_variety' => ['sometimes', new Enum(MealVariety::class)],
            'auto_fill_calories' => ['sometimes', 'boolean'],

            'physical_limitations' => ['sometimes', 'array'],
            'physical_limitations.*' => ['string', 'in:back,knee,shoulder,hip,wrist,neck,ankle'],
            'physical_limitations_note' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function userAttributes(): array
    {
        return array_intersect_key($this->validated(), array_flip(self::USER_FIELDS));
    }

    /**
     * @return array<string, mixed>
     */
    public function profileAttributes(): array
    {
        return array_diff_key($this->validated(), array_flip(self::USER_FIELDS));
    }
}
