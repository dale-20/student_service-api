<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\EnrollStudent;
use App\Actions\UpdateEnrollment;
use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentRequest;
use App\Http\Requests\EnrollmentUpdateRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;
use App\Models\Student;
use App\Support\ApiQuery;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Enrollment::class);
        [$sort, $direction] = ApiQuery::sort($request, ['enrollment_date', 'status'], 'enrollment_date');
        $query = Enrollment::query()->with(['student.program', 'courseOffering.course', 'courseOffering.academicTerm', 'grade'])->when($request->filled('search'), fn ($query) => $query->whereHas('student', fn ($query) => $query->where('student_number', 'like', '%'.$request->string('search').'%')->orWhere('first_name', 'like', '%'.$request->string('search').'%')->orWhere('last_name', 'like', '%'.$request->string('search').'%')))->when($request->filled('student_id'), fn ($query) => $query->where('student_id', $request->integer('student_id')))->when($request->filled('course_offering_id'), fn ($query) => $query->where('course_offering_id', $request->integer('course_offering_id')))->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))->when($request->filled('academic_term_id'), fn ($query) => $query->whereHas('courseOffering', fn ($query) => $query->where('academic_term_id', $request->integer('academic_term_id'))))->orderBy($sort, $direction);

        return EnrollmentResource::collection($query->paginate(ApiQuery::perPage($request))->withQueryString());
    }

    public function store(EnrollmentRequest $request, EnrollStudent $action)
    {
        $this->authorize('create', Enrollment::class);
        $student = Student::query()->findOrFail($request->integer('student_id'));
        $enrollments = $action->execute($student, $request->validated('course_offering_ids'));
        $enrollments->each->load(['student.program', 'courseOffering.course', 'courseOffering.academicTerm', 'grade']);

        return EnrollmentResource::collection($enrollments)->additional(['message' => 'Student enrolled successfully.']);
    }

    public function show(Enrollment $enrollment): EnrollmentResource
    {
        $this->authorize('view', $enrollment);

        return new EnrollmentResource($enrollment->load(['student.program', 'courseOffering.course', 'courseOffering.academicTerm', 'grade']));
    }

    public function update(EnrollmentUpdateRequest $request, Enrollment $enrollment, UpdateEnrollment $action): EnrollmentResource
    {
        $this->authorize('update', $enrollment);
        $enrollment = $action->execute($enrollment, $request->validated());

        return new EnrollmentResource($enrollment->load(['student.program', 'courseOffering.course', 'courseOffering.academicTerm', 'grade']));
    }
}
