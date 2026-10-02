<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RoleSlug;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LookupRequest;
use App\Http\Resources\AcademicTermResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\UserResource;
use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    public function __invoke(LookupRequest $request): JsonResponse
    {
        $linkedUser = $request->filled('student_id') ? Student::query()->findOrFail($request->validated('student_id'))->user_id : null;
        $instructor = $request->filled('course_offering_id') ? CourseOffering::query()->findOrFail($request->validated('course_offering_id'))->instructor_id : null;

        return response()->json(['data' => [
            'programs' => ProgramResource::collection(Program::query()->orderBy('code')->get())->resolve($request),
            'courses' => CourseResource::collection(Course::query()->orderBy('course_code')->get())->resolve($request),
            'academic_terms' => AcademicTermResource::collection(AcademicTerm::query()->latest('start_date')->get())->resolve($request),
            'instructors' => UserResource::collection(User::query()->with('role')->whereHas('role', fn ($query) => $query->where('slug', RoleSlug::Instructor))->where(fn ($query) => $query->where('status', UserStatus::Active)->orWhere('id', $instructor))->orderBy('name')->get())->resolve($request),
            'student_users' => UserResource::collection(User::query()->with('role')->whereHas('role', fn ($query) => $query->where('slug', RoleSlug::Student))->where(fn ($query) => $query->where(fn ($query) => $query->where('status', UserStatus::Active)->whereDoesntHave('student', fn ($query) => $query->withTrashed()))->orWhere('id', $linkedUser))->orderBy('name')->get())->resolve($request),
            'roles' => Role::query()->orderBy('name')->get(['id', 'name', 'slug']),
        ]]);
    }
}
