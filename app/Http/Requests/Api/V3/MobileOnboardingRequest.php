<?php

namespace App\Http\Requests\Api\V3;

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\CookingPreference;
use App\Enums\DietaryPreference;
use App\Enums\DietStyle;
use App\Enums\Gender;
use App\Enums\MealVariety;
use App\Enums\SkillLevel;
use App\Enums\TrainingPlace;
use App\Enums\UserSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class MobileOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower($this->email),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // User
            'email' => [
                'required',
                config('app.env') !== 'production'
                    ? 'email:rfc,filter'
                    : 'email:rfc,dns,strict,filter',
                'unique:users',
            ],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'language' => ['nullable', 'string', 'in:en,de'],
            'source' => ['sometimes', new Enum(UserSource::class)],
            'device_name' => ['sometimes', 'nullable', 'string', 'max:255'],

            // Body & training
            'birthdate' => ['required', 'date', 'before:-13 years', 'after:-120 years'],
            'gender' => ['required', new Enum(Gender::class)],
            'weight' => ['required', 'numeric', 'min:30', 'max:300'],
            'height' => ['required', 'numeric', 'min:100', 'max:250'],
            'body_goal' => ['required', Rule::in(array_map(fn ($g) => $g->value, BodyGoal::current()))],
            'skill_level' => ['required', new Enum(SkillLevel::class)],
            'activity_level' => ['required', new Enum(ActivityLevel::class)],
            'training_place' => ['required', new Enum(TrainingPlace::class)],
            'training_sessions' => ['required', 'integer', 'min:1', 'max:7'],
            'training_days' => ['nullable', 'array'],

            // Diet
            'dietary_preference' => ['required', new Enum(DietaryPreference::class)],
            'diet_style' => ['nullable', new Enum(DietStyle::class)],

            // Meal preferences
            'selected_meals' => ['sometimes', 'array'],
            'selected_meals.*' => ['string', 'in:breakfast,lunch,snack,dinner'],
            'dislikes' => ['sometimes', 'array'],
            'dislikes.*' => ['string', 'max:100'],
            'cooking_time' => ['sometimes', new Enum(CookingPreference::class)],
            'meal_variety' => ['sometimes', new Enum(MealVariety::class)],
            'meal_prep_enabled' => ['sometimes', 'boolean'],
            'favorite_recipes' => ['sometimes', 'array'],
            'favorite_recipes.*' => ['integer', 'exists:recipes,id'],

            // Limitations
            'has_limitations' => ['sometimes', 'boolean'],
            'limitations' => ['sometimes', 'array'],
            'limitations.*' => ['string', 'in:back,knee,shoulder,hip,wrist,neck,ankle'],
            'limitations_note' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please provide an email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
        ];
    }
}
