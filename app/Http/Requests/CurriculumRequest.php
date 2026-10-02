<?php

namespace App\Http\Requests;

use App\Enums\Semester;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurriculumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'courses' => ['present', 'array'],
            'courses.*' => ['array:course_id,year_level,semester,is_required'],
            'courses.*.course_id' => ['required', 'integer', 'distinct', Rule::exists('courses', 'id')->whereNull('deleted_at')],
            'courses.*.year_level' => ['nullable', 'integer', 'between:1,5'],
            'courses.*.semester' => ['nullable', Rule::enum(Semester::class)],
            'courses.*.is_required' => ['required', 'boolean'],
        ];
    }
}
