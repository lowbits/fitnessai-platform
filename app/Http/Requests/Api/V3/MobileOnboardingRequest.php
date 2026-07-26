<?php

namespace App\Http\Requests\Api\V3;

use App\Http\Requests\Api\V3\Concerns\OnboardingProfileRules;
use Illuminate\Foundation\Http\FormRequest;

class MobileOnboardingRequest extends FormRequest
{
    use OnboardingProfileRules;

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
            'email' => [
                'required',
                config('app.env') !== 'production'
                    ? 'email:rfc,filter'
                    : 'email:rfc,dns,strict,filter',
                'unique:users',
            ],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            ...$this->profileRules(),
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
