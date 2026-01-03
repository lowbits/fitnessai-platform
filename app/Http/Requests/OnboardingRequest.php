<?php

namespace App\Http\Requests;

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\DietType;
use App\Enums\Gender;
use App\Enums\SkillLevel;
use App\Enums\TrainingPlace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class OnboardingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower($this->email),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                config('app.env') === 'testing'
                    ? 'email:rfc,filter'
                    : 'email:rfc,dns,strict,filter',
                'unique:users',
            ],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'age' => ['required', 'integer', 'min:13', 'max:120'],
            'gender' => ['required', new Enum(Gender::class)],
            'weight' => ['required', 'numeric', 'min:30', 'max:300'],
            'height' => ['required', 'numeric', 'min:100', 'max:250'],
            'body_goal' => ['required', new Enum(BodyGoal::class)],
            'skill_level' => ['required', new Enum(SkillLevel::class)],
            'activity_level' => ['required', new Enum(ActivityLevel::class)],
            'training_place' => ['required', new Enum(TrainingPlace::class)],
            'diet_type' => ['required', new Enum(DietType::class)],
            'training_sessions' => ['required', 'integer', 'min:1', 'max:7'],
            'language' => ['nullable', 'string', 'in:en,de']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please provide an email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.regex' => 'Please provide a valid email address with a proper domain.',
        ];
    }
}

