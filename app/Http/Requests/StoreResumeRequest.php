<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'candidate';
    }

    public function rules(): array
    {
        return [
            'job_role_id' => ['required', 'exists:job_roles,id'],
            'resume_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'job_role_id.required' => 'Please select a target job role.',
            'job_role_id.exists' => 'The selected job role is invalid.',
            'resume_file.required' => 'Please choose a resume file.',
            'resume_file.mimes' => 'Resume file must be in PDF, DOC, or DOCX format.',
            'resume_file.max' => 'Resume file size must not be more than 5 MB.',
        ];
    }
}
