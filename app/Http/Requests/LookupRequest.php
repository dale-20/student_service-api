<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['sometimes', 'integer', Rule::exists('students', 'id')->whereNull('deleted_at')],
            'course_offering_id' => ['sometimes', 'integer', Rule::exists('course_offerings', 'id')->whereNull('deleted_at')],
        ];
    }
}
