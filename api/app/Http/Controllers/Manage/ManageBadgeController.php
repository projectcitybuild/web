<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\ManageBadgeRequest;
use App\Models\Eloquent\Badge;
use Illuminate\Http\JsonResponse;

class ManageBadgeController extends Controller
{
    public function index(): JsonResponse
    {
        $badges = Badge::cursorPaginate(config('api.page_size'));

        return response()->json($badges);
    }

    public function store(ManageBadgeRequest $request): JsonResponse
    {
        $badge = Badge::create($request->validated());

        return response()->json($badge);
    }

    public function show(string $id): JsonResponse
    {
        $badge = Badge::findOrFail($id);

        return response()->json($badge);
    }

    public function update(ManageBadgeRequest $request, string $id): JsonResponse
    {
        $badge = Badge::findOrFail($id);
        $badge->update($request->validated());
        $badge->save();

        return response()->json($badge);
    }

    public function destroy(string $id): JsonResponse
    {
        $badge = Badge::findOrFail($id);
        $badge->delete();

        return response()->json();
    }
}
