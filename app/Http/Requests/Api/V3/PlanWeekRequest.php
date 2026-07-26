<?php

namespace App\Http\Requests\Api\V3;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class PlanWeekRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['date' => $this->route('date') ?? now()->toDateString()]);
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

    public function anchorDate(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->validated('date'))->startOfDay();
    }
}
