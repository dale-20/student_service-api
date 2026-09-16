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
            $program->programCourses()->delete();
            $program->programCourses()->createMany($request->validated('courses'));
        });

        return (new ProgramResource($program->load(['programCourses.course'])->loadCount(['students', 'courses'])))->additional(['message' => 'Curriculum updated successfully.']);
    }
}
