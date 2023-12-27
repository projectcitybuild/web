<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Eloquent\Group;
use Illuminate\Http\JsonResponse;

class GroupController extends Controller
{
    public function index(): JsonResponse
    {
        $groups = Group::get();

        return response()->json($groups);
    }

    public function store(GroupRequest $request): JsonResponse
    {
        $group = Group::create($request->validated());

        return response()->json($group);
    }

    public function show(string $id): JsonResponse
    {
        $group = Group::findOrFail($id);

        return response()->json($group);
    }

    public function update(GroupRequest $request, string $id): JsonResponse
    {
        $group = Group::findOrFail($id);
        $group->update($request->validated());
        $group->save();

        return response()->json($group);
    }

    public function destroy(string $id): JsonResponse
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return response()->json();
    }
}
