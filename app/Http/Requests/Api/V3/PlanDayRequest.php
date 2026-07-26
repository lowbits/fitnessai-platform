<?php

namespace App\Http\Requests\Api\V3;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class PlanDayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['date' => $this->route('date')]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
        ];
    }

    public function planDate(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->validated('date'))->startOfDay();
    }
}
