<?php

namespace App\Http\Requests\Api\V3;

use App\Enums\ConsentSource;
use App\Enums\ConsentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsentRequest extends FormRequest
{
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
            'type' => ['required', Rule::enum(ConsentType::class)],
            'version' => ['required', Rule::in([config('consent.current_version')])],
            'source' => ['required', Rule::enum(ConsentSource::class)],
            'locale' => ['required', 'in:de,en'],
        ];
    }
}
