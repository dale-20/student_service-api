<?php

namespace App\Http\Requests;

use App\Enums\EnrollmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')->whereNull('deleted_at')],
            'course_offering_id' => ['required', 'integer', Rule::exists('course_offerings', 'id')->whereNull('deleted_at')],
            'enrollment_date' => ['required', 'date'],
            'status' => ['required', Rule::enum(EnrollmentStatus::class)],
        ];
    }
}
