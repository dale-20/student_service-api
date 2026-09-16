<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'enrollment_id' => $this->enrollment_id,
            'midterm_grade' => $this->midterm_grade === null ? null : (float) $this->midterm_grade,
            'final_grade' => $this->final_grade === null ? null : (float) $this->final_grade,
            'remarks' => $this->remarks,
        ];
    }
}
