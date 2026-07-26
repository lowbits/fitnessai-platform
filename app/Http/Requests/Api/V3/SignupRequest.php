<?php

namespace App\Http\Requests\Api\V3;

use App\Http\Requests\Api\V3\Concerns\OnboardingProfileRules;
use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
    use OnboardingProfileRules;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('email')) {
            $this->merge(['email' => strtolower($this->email)]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'auth' => ['required', 'array'],
            'auth.type' => ['required', 'in:password,apple,google'],
            'auth.password' => ['required_if:auth.type,password', 'string', 'min:8'],
            'auth.token' => ['required_unless:auth.type,password', 'string'],

            'name' => ['required_if:auth.type,password', 'string', 'max:255'],
            'email' => [
                'required_if:auth.type,password',
                'nullable',
                config('app.env') !== 'production'
                    ? 'email:rfc,filter'
                    : 'email:rfc,dns,strict,filter',
                'unique:users',
            ],

            ...$this->profileRules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered.',
        ];
    }
}
