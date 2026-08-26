<?php

namespace App\Http\Requests\Api\V3;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HealthDailySyncRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'active_energy_kcal' => ['required', 'integer', 'min:0', 'max:50000'],
            'steps' => ['required', 'integer', 'min:0', 'max:500000'],
            'workouts' => ['present', 'array'],
            'workouts.*.type' => ['nullable', 'string', 'max:100'],
            'workouts.*.start' => ['nullable', 'date'],
            'workouts.*.end' => ['nullable', 'date'],
            'workouts.*.energy_kcal' => ['nullable', 'integer', 'min:0', 'max:50000'],
            'workouts.*.source' => ['nullable', 'string', 'max:100'],
        ];
    }
}
