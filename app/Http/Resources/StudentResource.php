<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'program_id' => $this->program_id,
            'student_number' => $this->student_number,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix,
            'birth_date' => $this->birth_date?->toDateString(),
            'email' => $this->email,
            'contact_number' => $this->contact_number,
            'address' => $this->address,
            'year_level' => $this->year_level,
            'status' => $this->status,
            'program' => new ProgramResource($this->whenLoaded('program')),
            'user' => $this->whenLoaded('user', fn () => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'status' => $this->user->status,
            ] : null),
            'enrollments_count' => $this->whenCounted('enrollments'),
        ];
    }
}
