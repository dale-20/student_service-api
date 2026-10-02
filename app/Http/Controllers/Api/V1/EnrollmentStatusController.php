<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\UpdateEnrollment;
use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentStatusRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;

class EnrollmentStatusController extends Controller
{
    public function update(EnrollmentStatusRequest $request, Enrollment $enrollment, UpdateEnrollment $action): EnrollmentResource
    {
        $this->authorize('update', $enrollment);
        $enrollment = $action->execute($enrollment, $request->validated());

        return (new EnrollmentResource($enrollment->refresh()->load(['student.program', 'courseOffering.course', 'courseOffering.academicTerm', 'grade'])))->additional(['message' => 'Enrollment status updated successfully.']);
    }
}
