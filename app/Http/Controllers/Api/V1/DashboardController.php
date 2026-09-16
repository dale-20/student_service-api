<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\EnrollmentStatus;
use App\Enums\RoleSlug;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseOfferingResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\StudentResource;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole(RoleSlug::Student)) {
            $student = $user->student()->with('program')->firstOrFail();
            $enrollments = $student->enrollments()->where('status', EnrollmentStatus::Enrolled)->with(['student.program', 'courseOffering.course', 'courseOffering.academicTerm', 'grade'])->get();

            return response()->json(['data' => ['kind' => 'student', 'student' => (new StudentResource($student))->resolve($request), 'enrollments' => EnrollmentResource::collection($enrollments)->resolve($request), 'current_units' => $enrollments->sum(fn ($item) => (float) $item->courseOffering->course->units)]]);
        }

        if ($user->hasRole(RoleSlug::Instructor)) {
            $offerings = CourseOffering::query()->where('instructor_id', $user->id)->with(['course', 'academicTerm', 'instructor.role', 'schedules'])->withCount('enrollments')->get();

            return response()->json(['data' => ['kind' => 'instructor', 'offerings' => CourseOfferingResource::collection($offerings)->resolve($request), 'total_students' => $offerings->sum('enrollments_count'), 'grades_pending' => Enrollment::query()->whereHas('courseOffering', fn ($query) => $query->where('instructor_id', $user->id))->whereDoesntHave('grade')->count()]]);
        }

        $recent = Enrollment::query()->with(['student.program', 'courseOffering.course', 'courseOffering.academicTerm', 'grade'])->latest()->limit(5)->get();

        return response()->json(['data' => ['kind' => $user->role->slug->value, 'total_students' => Student::query()->count(), 'programs' => Program::query()->count(), 'course_offerings' => CourseOffering::query()->count(), 'active_enrollments' => Enrollment::query()->where('status', EnrollmentStatus::Enrolled)->count(), 'recent_enrollments' => EnrollmentResource::collection($recent)->resolve($request)]]);
    }
}
