<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'students_count' => $this->whenCounted('students'),
            'courses_count' => $this->whenCounted('courses'),
            'curriculum' => ProgramCourseResource::collection($this->whenLoaded('programCourses')),
        ];
    }
}
