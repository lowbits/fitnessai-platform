<?php

namespace App\Http\Requests\Api\V3;

use Illuminate\Foundation\Http\FormRequest;

class CoachMessageRequest extends FormRequest
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
            'conversation_id' => ['nullable', 'string', 'size:36'],
            'message' => ['required', 'string', 'max:2000'],
            'context' => ['nullable', 'array'],
            'context.type' => ['required_with:context', 'string', 'in:meal_replace'],
            'context.meal_id' => ['required_if:context.type,meal_replace', 'integer'],
        ];
    }
}
