<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\SaveOfferingGrades;
use App\Http\Controllers\Controller;
use App\Http\Requests\GradeBatchRequest;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\GradeResource;
use App\Models\CourseOffering;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request, CourseOffering $courseOffering)
    {
        $this->authorize('manageGrades', $courseOffering);
        $enrollments = $courseOffering->enrollments()->with(['student.program', 'grade', 'courseOffering.course', 'courseOffering.academicTerm'])->orderByRaw('(select last_name from students where students.id = enrollments.student_id)')->get();

        return EnrollmentResource::collection($enrollments);
    }

    public function update(GradeBatchRequest $request, CourseOffering $courseOffering, SaveOfferingGrades $action)
    {
        $this->authorize('manageGrades', $courseOffering);
        $grades = $action->execute($courseOffering, $request->validated('grades'));

        return GradeResource::collection($grades)->additional(['message' => 'Grades saved successfully.']);
    }
}
