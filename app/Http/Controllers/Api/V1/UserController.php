<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiQuery;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        [$sort, $direction] = ApiQuery::sort($request, ['name', 'email', 'status', 'last_login_at'], 'name');
        $users = User::query()->with('role')->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$request->string('search').'%')->orWhere('email', 'like', '%'.$request->string('search').'%')))->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))->when($request->filled('role'), fn ($query) => $query->whereHas('role', fn ($query) => $query->where('slug', $request->string('role'))))->orderBy($sort, $direction)->paginate(ApiQuery::perPage($request))->withQueryString();

        return UserResource::collection($users);
    }

    public function store(UserRequest $request): UserResource
    {
        $this->authorize('create', User::class);
        $user = User::query()->create([...$request->safe()->except('password_confirmation'), 'must_change_password' => true]);

        return (new UserResource($user->load('role')))->additional(['message' => 'User created successfully.']);
    }

    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($user->load(['role', 'student.program']));
    }

    public function update(UserRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);
        $data = $request->safe()->except(['password', 'password_confirmation']);
        $user->update($data);

        return (new UserResource($user->refresh()->load('role')))->additional(['message' => 'User updated successfully.']);
    }
}
