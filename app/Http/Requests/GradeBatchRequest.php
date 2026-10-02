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
            'grades.*' => ['array:enrollment_id,midterm_grade,final_grade,remarks'],
            'grades.*.enrollment_id' => ['required', 'integer', 'distinct', Rule::exists('enrollments', 'id')->whereNull('deleted_at')],
            'grades.*.midterm_grade' => ['nullable', 'numeric', 'between:1,5'],
            'grades.*.final_grade' => ['nullable', 'numeric', 'between:1,5'],
            'grades.*.remarks' => ['nullable', Rule::enum(GradeRemark::class)],
        ];
    }
}
