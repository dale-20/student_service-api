<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseOfferingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'academic_term_id' => $this->academic_term_id,
            'instructor_id' => $this->instructor_id,
            'section' => $this->section,
            'room' => $this->room,
            'schedule' => $this->schedule,
            'capacity' => $this->capacity,
            'status' => $this->status,
            'enrolled_count' => $this->when(isset($this->enrolled_count), fn (): int => (int) $this->enrolled_count),
            'course' => new CourseResource($this->whenLoaded('course')),
            'academic_term' => new AcademicTermResource($this->whenLoaded('academicTerm')),
            'instructor' => new UserResource($this->whenLoaded('instructor')),
            'schedules' => CourseOfferingScheduleResource::collection($this->whenLoaded('schedules')),
        ];
    }
}
