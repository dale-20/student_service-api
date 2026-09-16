<?php

namespace App\Http\Requests;

use App\Enums\GradeRemark;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GradeBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'grades' => ['required', 'array', 'min:1'],
            'grades.*.enrollment_id' => ['required', 'integer', 'distinct', 'exists:enrollments,id'],
            'grades.*.midterm_grade' => ['nullable', 'numeric', 'between:1,5'],
            'grades.*.final_grade' => ['nullable', 'numeric', 'between:1,5'],
            'grades.*.remarks' => ['nullable', Rule::enum(GradeRemark::class)],
        ];
    }
}
