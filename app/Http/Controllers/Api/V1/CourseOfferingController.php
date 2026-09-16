<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\SaveCourseOffering;
use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseOfferingRequest;
use App\Http\Resources\CourseOfferingResource;
use App\Models\CourseOffering;
use App\Support\ApiQuery;
use Illuminate\Http\Request;

class CourseOfferingController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', CourseOffering::class);
        [$sort, $direction] = ApiQuery::sort($request, ['section', 'capacity', 'status'], 'section');
        $query = CourseOffering::query()->with(['course', 'academicTerm', 'instructor.role', 'schedules'])->withCount(['enrollments as enrolled_count' => fn ($query) => $query->whereIn('status', [EnrollmentStatus::Enrolled->value, EnrollmentStatus::Completed->value])])->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query->where('section', 'like', '%'.$request->string('search').'%')->orWhereHas('course', fn ($query) => $query->where('course_code', 'like', '%'.$request->string('search').'%')->orWhere('course_title', 'like', '%'.$request->string('search').'%'))))->when($request->filled('academic_term_id'), fn ($query) => $query->where('academic_term_id', $request->integer('academic_term_id')))->when($request->filled('course_id'), fn ($query) => $query->where('course_id', $request->integer('course_id')))->when($request->filled('instructor_id'), fn ($query) => $query->where('instructor_id', $request->integer('instructor_id')))->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))->orderBy($sort, $direction);

        return CourseOfferingResource::collection($query->paginate(ApiQuery::perPage($request))->withQueryString());
    }

    public function store(CourseOfferingRequest $request, SaveCourseOffering $action): CourseOfferingResource
    {
        $this->authorize('create', CourseOffering::class);
        $offering = $action->execute($request->validated());

        return (new CourseOfferingResource($this->load($offering)))->additional(['message' => 'Course offering created successfully.']);
    }

    public function show(CourseOffering $courseOffering): CourseOfferingResource
    {
        $this->authorize('view', $courseOffering);

        return new CourseOfferingResource($this->load($courseOffering));
    }

    public function update(CourseOfferingRequest $request, CourseOffering $courseOffering, SaveCourseOffering $action): CourseOfferingResource
    {
        $this->authorize('update', $courseOffering);
        $offering = $action->execute($request->validated(), $courseOffering);

        return (new CourseOfferingResource($this->load($offering)))->additional(['message' => 'Course offering updated successfully.']);
    }

    private function load(CourseOffering $offering): CourseOffering
    {
        return $offering->load(['course', 'academicTerm', 'instructor.role', 'schedules'])->loadCount(['enrollments as enrolled_count' => fn ($query) => $query->whereIn('status', [EnrollmentStatus::Enrolled->value, EnrollmentStatus::Completed->value])]);
    }
}
