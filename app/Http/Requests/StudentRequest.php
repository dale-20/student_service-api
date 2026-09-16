<?php

namespace App\Http\Requests;

use App\Enums\RoleSlug;
use App\Enums\StudentStatus;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $student = $this->route('student');

        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id', Rule::unique('students', 'user_id')->ignore($student), function (string $attribute, mixed $value, Closure $fail): void {
                if ($value && ! User::query()->whereKey($value)->whereHas('role', fn ($query) => $query->where('slug', RoleSlug::Student))->where('status', 'active')->exists()) {
                    $fail('The selected account must be an active student account.');
                }
            }],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'student_number' => ['required', 'string', 'max:20', Rule::unique('students')->ignore($student)],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
            'year_level' => ['required', 'integer', 'between:1,5'],
            'status' => ['required', Rule::enum(StudentStatus::class)],
        ];
    }
}
