<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CurriculumRequest;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use Illuminate\Support\Facades\DB;

class CurriculumController extends Controller
{
    public function update(CurriculumRequest $request, Program $program): ProgramResource
    {
        $this->authorize('update', $program);
        DB::transaction(function () use ($request, $program): void {
            $program = Program::query()->lockForUpdate()->findOrFail($program->id);
            $retained = [];
            foreach ($request->validated('courses') as $row) {
                $entry = $program->programCourses()->withTrashed()->firstOrNew(['course_id' => $row['course_id']]);
                $entry->fill($row)->save();
                if ($entry->trashed()) {
                    $entry->restore();
                }
                $retained[] = $entry->id;
            }
            $program->programCourses()->whereNotIn('id', $retained)->delete();
        });

        return (new ProgramResource($program->load(['programCourses.course'])->loadCount(['students', 'courses'])))->additional(['message' => 'Curriculum updated successfully.']);
    }
}
