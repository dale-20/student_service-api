<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseOfferingResource;
use App\Models\CourseOffering;
use App\Support\ApiQuery;
use Illuminate\Http\Request;

class InstructorOfferingController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseOffering::query()->where('instructor_id', $request->user()->id)->with(['course', 'academicTerm', 'instructor.role', 'schedules'])->withCount(['enrollments as enrolled_count' => fn ($query) => $query->whereIn('status', [EnrollmentStatus::Enrolled->value, EnrollmentStatus::Completed->value])]);

        return CourseOfferingResource::collection($query->paginate(ApiQuery::perPage($request)));
    }
}
