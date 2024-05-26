<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\ManageGroupStoreRequest;
use App\Http\Requests\Manage\ManageGroupUpdateRequest;
use App\Models\Group;
use Illuminate\Http\JsonResponse;

class ManageGroupController extends Controller
{
    public function index(): JsonResponse
    {
        $groups = Group::cursorPaginate(config('api.page_size'));

        return response()->json($groups);
    }

    public function store(ManageGroupStoreRequest $request): JsonResponse
    {
        $group = Group::create($request->validated());

        return response()->json($group);
    }

    public function show(string $id): JsonResponse
    {
        $group = Group::findOrFail($id);

        return response()->json($group);
    }

    public function update(ManageGroupUpdateRequest $request, string $id): JsonResponse
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
