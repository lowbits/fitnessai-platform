<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleWorkoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Use the policy to authorize
        return $this->user()->can('update', $this->route('workout'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'target_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'force' => [
                'nullable',
                'boolean',
            ],
        ];
    }


    /**
     * Get the target date from request
     */
    public function getTargetDate(): string
    {
        return $this->input('target_date');
    }

    /**
     * Get the force flag from request
     */
    public function shouldForce(): bool
    {
        return $this->boolean('force', false);
    }
}
