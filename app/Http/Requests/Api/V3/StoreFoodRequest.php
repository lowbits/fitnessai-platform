<?php

namespace App\Http\Requests\Api\V3;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoodRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'kcal' => ['required', 'numeric', 'min:0', 'max:10000'],
            'protein_g' => ['nullable', 'numeric', 'min:0'],
            'carbs_g' => ['nullable', 'numeric', 'min:0'],
            'fat_g' => ['nullable', 'numeric', 'min:0'],
            'fiber_g' => ['nullable', 'numeric', 'min:0'],
            'sugar_g' => ['nullable', 'numeric', 'min:0'],
            'sat_fat_g' => ['nullable', 'numeric', 'min:0'],
            'salt_g' => ['nullable', 'numeric', 'min:0'],
            'serving_size' => ['nullable', 'numeric', 'min:0'],
            'serving_unit' => ['nullable', 'string', 'max:16'],
        ];
    }
}
