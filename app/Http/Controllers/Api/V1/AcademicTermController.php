<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicTermRequest;
use App\Http\Resources\AcademicTermResource;
use App\Models\AcademicTerm;
use App\Support\ApiQuery;
use Illuminate\Http\Request;

class AcademicTermController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', AcademicTerm::class);
        [$sort, $direction] = ApiQuery::sort($request, ['academic_year', 'term', 'start_date', 'status'], 'start_date');

        return AcademicTermResource::collection(AcademicTerm::query()->when($request->filled('search'), fn ($query) => $query->where('academic_year', 'like', '%'.$request->string('search').'%'))->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))->orderBy($sort, $direction)->paginate(ApiQuery::perPage($request))->withQueryString());
    }

    public function store(AcademicTermRequest $request): AcademicTermResource
    {
        $this->authorize('create', AcademicTerm::class);

        return (new AcademicTermResource(AcademicTerm::query()->create($request->validated())))->additional(['message' => 'Academic term created successfully.']);
    }

    public function show(AcademicTerm $academicTerm): AcademicTermResource
    {
        $this->authorize('view', $academicTerm);

        return new AcademicTermResource($academicTerm);
    }

    public function update(AcademicTermRequest $request, AcademicTerm $academicTerm): AcademicTermResource
    {
        $this->authorize('update', $academicTerm);
        $academicTerm->update($request->validated());

        return (new AcademicTermResource($academicTerm->refresh()))->additional(['message' => 'Academic term updated successfully.']);
    }
}
