<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'course_offering_ids' => ['required', 'array', 'min:1'],
            'course_offering_ids.*' => ['required', 'integer', 'distinct', 'exists:course_offerings,id'],
        ];
    }
}
