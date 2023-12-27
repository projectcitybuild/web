<?php

namespace App\Http\Controllers;

use App\Http\Requests\BadgeRequest;
use App\Models\Eloquent\Badge;
use Illuminate\Http\JsonResponse;

class BadgeController extends Controller
{
    public function index(): JsonResponse
    {
        $badges = Badge::get();

        return response()->json($badges);
    }

    public function store(BadgeRequest $request): JsonResponse
    {
        $badge = Badge::create($request->validated());

        return response()->json($badge);
    }

    public function show(string $id): JsonResponse
    {
        $badge = Badge::findOrFail($id);

        return response()->json($badge);
    }

    public function update(BadgeRequest $request, string $id): JsonResponse
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
