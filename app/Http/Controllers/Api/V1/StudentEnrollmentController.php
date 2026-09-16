<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EnrollmentResource;
use App\Support\ApiQuery;
use Illuminate\Http\Request;

class StudentEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user()->student()->firstOrFail();

        return EnrollmentResource::collection($student->enrollments()->with(['student.program', 'courseOffering.course', 'courseOffering.academicTerm', 'courseOffering.schedules', 'grade'])->latest('enrollment_date')->paginate(ApiQuery::perPage($request)));
    }
}
