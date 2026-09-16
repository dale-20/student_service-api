<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Support\ApiQuery;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Course::class);
        [$sort, $direction] = ApiQuery::sort($request, ['course_code', 'course_title', 'units', 'status'], 'course_code');

        return CourseResource::collection(Course::query()->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query->where('course_code', 'like', '%'.$request->string('search').'%')->orWhere('course_title', 'like', '%'.$request->string('search').'%')))->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))->orderBy($sort, $direction)->paginate(ApiQuery::perPage($request))->withQueryString());
    }

    public function store(CourseRequest $request): CourseResource
    {
        $this->authorize('create', Course::class);

        return (new CourseResource(Course::query()->create($request->validated())))->additional(['message' => 'Course created successfully.']);
    }

    public function show(Course $course): CourseResource
    {
        $this->authorize('view', $course);

        return new CourseResource($course);
    }

    public function update(CourseRequest $request, Course $course): CourseResource
    {
        $this->authorize('update', $course);
        $course->update($request->validated());

        return (new CourseResource($course->refresh()))->additional(['message' => 'Course updated successfully.']);
    }
}
