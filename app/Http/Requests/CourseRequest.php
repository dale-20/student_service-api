<?php

namespace App\Http\Requests;

use App\Enums\CourseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_code' => ['required', 'string', 'max:20', Rule::unique('courses')->ignore($this->route('course'))],
            'course_title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'units' => ['required', 'numeric', 'gt:0', 'max:99.99'],
            'status' => ['required', Rule::enum(CourseStatus::class)],
        ];
    }
}
