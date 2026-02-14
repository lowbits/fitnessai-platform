<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddWorkoutExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('workout'));
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'exercise_id' => ['required', 'integer', 'exists:exercises,id'],
            'sets' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'reps' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'duration_seconds' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'tempo' => ['sometimes', 'nullable', 'string'],
            'rpe' => ['sometimes', 'nullable', 'string'],
            'rest_seconds' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
