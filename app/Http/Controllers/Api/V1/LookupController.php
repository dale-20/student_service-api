<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AcademicTermStatus;
use App\Enums\CourseStatus;
use App\Enums\ProgramStatus;
use App\Enums\RoleSlug;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\AcademicTermResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\UserResource;
use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\Program;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(['data' => [
            'programs' => ProgramResource::collection(Program::query()->where('status', ProgramStatus::Active)->orderBy('code')->get())->resolve($request),
            'courses' => CourseResource::collection(Course::query()->where('status', CourseStatus::Active)->orderBy('course_code')->get())->resolve($request),
            'academic_terms' => AcademicTermResource::collection(AcademicTerm::query()->where('status', AcademicTermStatus::Active)->latest('start_date')->get())->resolve($request),
            'instructors' => UserResource::collection(User::query()->with('role')->where('status', UserStatus::Active)->whereHas('role', fn ($query) => $query->where('slug', RoleSlug::Instructor))->orderBy('name')->get())->resolve($request),
            'student_users' => UserResource::collection(User::query()->with('role')->where('status', UserStatus::Active)->whereHas('role', fn ($query) => $query->where('slug', RoleSlug::Student))->whereDoesntHave('student')->orderBy('name')->get())->resolve($request),
            'roles' => Role::query()->orderBy('name')->get(['id', 'name', 'slug']),
        ]]);
    }
}
