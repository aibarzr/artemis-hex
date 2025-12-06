<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
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
            'candidate_name' => ['required', 'string', 'max:255'],
            'candidate_email' => ['required', 'email', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:50'],
            'cv_path' => ['nullable', 'string', 'max:500'],
            'cover_letter' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'candidate_name.required' => 'The candidate name is required.',
            'candidate_name.max' => 'The candidate name cannot exceed 255 characters.',
            'candidate_email.required' => 'The candidate email is required.',
            'candidate_email.email' => 'The candidate email must be a valid email address.',
            'position.required' => 'The position is required.',
            'years_of_experience.required' => 'The years of experience is required.',
            'years_of_experience.integer' => 'The years of experience must be an integer.',
            'years_of_experience.min' => 'The years of experience cannot be negative.',
            'years_of_experience.max' => 'The years of experience cannot exceed 50 years.',
            'cv_path.max' => 'The CV path cannot exceed 500 characters.',
            'cover_letter.max' => 'The cover letter cannot exceed 5000 characters.',
        ];
    }
}
