<?php

namespace App\Http\Requests\Api\V3;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends FormRequest
{
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
            'auth.password' => ['required_if:auth.type,password', 'string'],
            'auth.token' => ['required_unless:auth.type,password', 'string'],
            'email' => ['required_if:auth.type,password', 'nullable', 'email'],
            'device_name' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
