<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AcademicTermResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicRecordController extends Controller
{
    public function show(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);
        $student->load(['program', 'user']);
        $enrollments = $student->enrollments()->with(['courseOffering.course', 'courseOffering.academicTerm', 'grade'])->get();
        $terms = $enrollments->groupBy('courseOffering.academic_term_id')->map(function ($items) use ($request): array {
            $term = $items->first()->courseOffering->academicTerm;

            return [
                'term' => (new AcademicTermResource($term))->resolve($request),
                'enrollments' => EnrollmentResource::collection($items)->resolve($request),
                'total_units' => $items->sum(fn ($enrollment) => (float) $enrollment->courseOffering->course->units),
            ];
        })->values();

        return response()->json(['data' => [
            'student' => (new StudentResource($student))->resolve($request),
            'terms' => $terms,
        ]]);
    }
}
