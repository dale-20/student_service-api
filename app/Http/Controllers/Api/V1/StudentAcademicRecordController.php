<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AcademicTermResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\StudentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentAcademicRecordController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $student = $request->user()->student()->with(['program', 'user'])->firstOrFail();
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
