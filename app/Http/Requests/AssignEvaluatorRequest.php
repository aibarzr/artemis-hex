<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignEvaluatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'evaluator_id' => ['required', 'integer', 'exists:evaluators,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'evaluator_id.required' => 'The evaluator ID is required.',
            'evaluator_id.integer' => 'The evaluator ID must be an integer.',
            'evaluator_id.exists' => 'The selected evaluator does not exist.',
        ];
    }
}
