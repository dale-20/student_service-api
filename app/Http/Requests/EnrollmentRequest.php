<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')->whereNull('deleted_at')],
            'course_offering_ids' => ['required', 'array', 'min:1'],
            'course_offering_ids.*' => ['required', 'integer', 'distinct', Rule::exists('course_offerings', 'id')->whereNull('deleted_at')],
        ];
    }
}
