<?php

namespace App\Http\Requests;

use App\Enums\AcademicTermStatus;
use App\Enums\AcademicTermType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicTermRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'term' => ['required', Rule::enum(AcademicTermType::class), Rule::unique('academic_terms')->where(fn ($query) => $query->where('academic_year', $this->string('academic_year')))->ignore($this->route('academicTerm'))],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::enum(AcademicTermStatus::class)],
        ];
    }
}
