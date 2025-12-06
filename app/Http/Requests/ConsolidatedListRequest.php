<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsolidatedListRequest extends FormRequest
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
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'order_by' => ['nullable', 'string', 'in:candidate_name,candidate_email,years_of_experience,created_at'],
            'order_direction' => ['nullable', 'string', 'in:asc,desc'],
            'filter_evaluator_id' => ['nullable', 'integer', 'exists:evaluators,id'],
            'filter_min_experience' => ['nullable', 'integer', 'min:0'],
            'filter_max_experience' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'page.integer' => 'The page must be an integer.',
            'page.min' => 'The page must be at least 1.',
            'per_page.integer' => 'The per page value must be an integer.',
            'per_page.min' => 'The per page value must be at least 1.',
            'per_page.max' => 'The per page value cannot exceed 100.',
            'order_by.in' => 'The order by field must be one of: candidate_name, candidate_email, years_of_experience, created_at.',
            'order_direction.in' => 'The order direction must be either asc or desc.',
            'filter_evaluator_id.exists' => 'The selected evaluator does not exist.',
        ];
    }
}
