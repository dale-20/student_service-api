<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentStatusRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class EnrollmentStatusController extends Controller
{
    public function update(EnrollmentStatusRequest $request, Enrollment $enrollment): EnrollmentResource
    {
        $this->authorize('update', $enrollment);
        if ($enrollment->status !== EnrollmentStatus::Enrolled) {
            throw new ConflictHttpException('Only active enrollments may change status.');
        }
        $enrollment->update($request->validated());

        return (new EnrollmentResource($enrollment->refresh()->load(['student.program', 'courseOffering.course', 'courseOffering.academicTerm', 'grade'])))->additional(['message' => 'Enrollment status updated successfully.']);
    }
}
