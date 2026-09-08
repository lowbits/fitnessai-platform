<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewsletterSubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $locale = $this->input('locale');
        if (in_array($locale, ['en', 'de'], true)) {
            app()->setLocale($locale);
        }

        if ($this->has('email')) {
            $this->merge(['email' => mb_strtolower(trim((string) $this->email))]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,filter', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'locale' => ['nullable', 'in:en,de'],
            'consent' => ['accepted'],
            'source' => ['nullable', 'string', 'max:64'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_content' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'consent.accepted' => __('newsletter.consent_required'),
        ];
    }
}
