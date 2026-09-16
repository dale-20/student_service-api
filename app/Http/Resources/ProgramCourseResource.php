<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramCourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'program_id' => $this->program_id,
            'course_id' => $this->course_id,
            'year_level' => $this->year_level,
            'semester' => $this->semester,
            'is_required' => $this->is_required,
            'course' => new CourseResource($this->whenLoaded('course')),
        ];
    }
}
