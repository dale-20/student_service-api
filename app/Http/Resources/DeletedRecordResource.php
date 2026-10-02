<?php

namespace App\Http\Resources;

use App\Enums\RecoverableType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeletedRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $type = RecoverableType::forModel($this->resource);
        [$label, $detail] = match ($type) {
            RecoverableType::Users => [$this->name, $this->email],
            RecoverableType::Students => [$this->first_name.' '.$this->last_name, $this->student_number],
            RecoverableType::Programs => [$this->name, $this->code],
            RecoverableType::Courses => [$this->course_title, $this->course_code],
            RecoverableType::AcademicTerms => [$this->academic_year, str_replace('_', ' ', $this->term->value)],
            RecoverableType::CourseOfferings => [$this->course?->course_code.' / '.$this->section, $this->course?->course_title],
            RecoverableType::Enrollments => [$this->student?->first_name.' '.$this->student?->last_name, $this->courseOffering?->course?->course_code.' / '.$this->courseOffering?->section],
            RecoverableType::Curriculum => [$this->course?->course_code.' / '.$this->course?->course_title, $this->program?->name],
            RecoverableType::Schedules => [ucfirst($this->day_of_week->value).' '.$this->start_time.'–'.$this->end_time, $this->courseOffering?->course?->course_code.' / '.$this->courseOffering?->section],
        };

        return [
            'id' => $this->id,
            'type' => $type->value,
            'label' => $label,
            'detail' => $detail,
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
