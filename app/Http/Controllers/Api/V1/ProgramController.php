<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramRequest;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use App\Support\ApiQuery;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Program::class);
        [$sort, $direction] = ApiQuery::sort($request, ['code', 'name', 'status'], 'code');

        return ProgramResource::collection(Program::query()->withCount(['students', 'courses'])->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query->where('code', 'like', '%'.$request->string('search').'%')->orWhere('name', 'like', '%'.$request->string('search').'%')))->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))->orderBy($sort, $direction)->paginate(ApiQuery::perPage($request))->withQueryString());
    }

    public function store(ProgramRequest $request): ProgramResource
    {
        $this->authorize('create', Program::class);

        return (new ProgramResource(Program::query()->create($request->validated())))->additional(['message' => 'Program created successfully.']);
    }

    public function show(Program $program): ProgramResource
    {
        $this->authorize('view', $program);

        return new ProgramResource($program->load(['programCourses.course'])->loadCount(['students', 'courses']));
    }

    public function update(ProgramRequest $request, Program $program): ProgramResource
    {
        $this->authorize('update', $program);
        $program->update($request->validated());

        return (new ProgramResource($program->refresh()->loadCount(['students', 'courses'])))->additional(['message' => 'Program updated successfully.']);
    }
}
