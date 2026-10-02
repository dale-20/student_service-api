<?php

namespace App\Http\Requests;

use App\Enums\CourseOfferingStatus;
use App\Enums\DayOfWeek;
use App\Enums\RoleSlug;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CourseOfferingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $offering = $this->route('courseOffering');

        return [
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')->whereNull('deleted_at')],
            'academic_term_id' => ['required', 'integer', Rule::exists('academic_terms', 'id')->whereNull('deleted_at')],
            'instructor_id' => ['nullable', 'integer', Rule::exists('users', 'id')->whereNull('deleted_at'), function (string $attribute, mixed $value, Closure $fail): void {
                if ($value && ! User::query()->whereKey($value)->whereHas('role', fn ($query) => $query->where('slug', RoleSlug::Instructor))->when((int) $value !== $this->route('courseOffering')?->instructor_id, fn ($query) => $query->where('status', 'active'))->exists()) {
                    $fail('The selected user must be an active instructor.');
                }
            }],
            'section' => ['required', 'string', 'max:50', Rule::unique('course_offerings')->where(fn ($query) => $query->where('course_id', $this->integer('course_id'))->where('academic_term_id', $this->integer('academic_term_id')))->ignore($offering)],
            'room' => ['nullable', 'string', 'max:50'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'status' => ['required', Rule::enum(CourseOfferingStatus::class)],
            'schedules' => ['present', 'array'],
            'schedules.*' => ['array:day_of_week,start_time,end_time,room'],
            'schedules.*.day_of_week' => ['required', Rule::enum(DayOfWeek::class)],
            'schedules.*.start_time' => ['required', 'date_format:H:i'],
            'schedules.*.end_time' => ['required', 'date_format:H:i', 'after:schedules.*.start_time'],
            'schedules.*.room' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $schedules = collect($this->input('schedules', []));
            $keys = $schedules->map(fn (array $schedule): string => implode('|', [
                $schedule['day_of_week'] ?? '',
                $schedule['start_time'] ?? '',
                $schedule['end_time'] ?? '',
            ]));

            if ($keys->unique()->count() !== $keys->count()) {
                $validator->errors()->add('schedules', 'Duplicate schedules are not allowed.');
            }
        }];
    }
}
