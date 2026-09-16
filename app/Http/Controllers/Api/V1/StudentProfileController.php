<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use Illuminate\Http\Request;

class StudentProfileController extends Controller
{
    public function show(Request $request): StudentResource
    {
        return new StudentResource($request->user()->student()->with(['program', 'user'])->firstOrFail());
    }
}
