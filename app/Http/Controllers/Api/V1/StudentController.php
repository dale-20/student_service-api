<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Support\ApiQuery;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Student::class);
        [$sort, $direction] = ApiQuery::sort($request, ['student_number', 'first_name', 'last_name', 'year_level', 'status'], 'last_name');
        $students = Student::query()->with('program')->withCount('enrollments')->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query->where('student_number', 'like', '%'.$request->string('search').'%')->orWhere('first_name', 'like', '%'.$request->string('search').'%')->orWhere('last_name', 'like', '%'.$request->string('search').'%')->orWhere('email', 'like', '%'.$request->string('search').'%')))->when($request->filled('program_id'), fn ($query) => $query->where('program_id', $request->integer('program_id')))->when($request->filled('year_level'), fn ($query) => $query->where('year_level', $request->integer('year_level')))->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))->orderBy($sort, $direction)->paginate(ApiQuery::perPage($request))->withQueryString();

        return StudentResource::collection($students);
    }

    public function store(StudentRequest $request): StudentResource
    {
        $this->authorize('create', Student::class);
        $student = Student::query()->create($request->validated());

        return (new StudentResource($student->load(['program', 'user'])))->additional(['message' => 'Student created successfully.']);
    }

    public function show(Student $student): StudentResource
    {
        $this->authorize('view', $student);

        return new StudentResource($student->load(['program', 'user'])->loadCount('enrollments'));
    }

    public function update(StudentRequest $request, Student $student): StudentResource
    {
        $this->authorize('update', $student);
        $student->update($request->validated());

        return (new StudentResource($student->refresh()->load(['program', 'user'])))->additional(['message' => 'Student updated successfully.']);
    }
}
