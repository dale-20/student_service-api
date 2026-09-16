<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'course_offering_id' => $this->course_offering_id,
            'enrollment_date' => $this->enrollment_date->toDateString(),
            'status' => $this->status,
            'student' => new StudentResource($this->whenLoaded('student')),
            'course_offering' => new CourseOfferingResource($this->whenLoaded('courseOffering')),
            'grade' => new GradeResource($this->whenLoaded('grade')),
        ];
    }
}
