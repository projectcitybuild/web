<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\ManagePlayerWarningRequest;
use App\Models\PlayerWarning;
use Illuminate\Http\JsonResponse;

class ManagePlayerWarningController extends Controller
{
    public function index(): JsonResponse
    {
        $warnings = PlayerWarning::cursorPaginate(config('api.page_size'));

        return response()->json($warnings);
    }

    public function store(ManagePlayerWarningRequest $request): JsonResponse
    {
        $warning = PlayerWarning::create($request->validated());

        return response()->json($warning);
    }

    public function show(string $id): JsonResponse
    {
        $warning = PlayerWarning::findOrFail($id);

        return response()->json($warning);
    }

    public function update(ManagePlayerWarningRequest $request, string $id): JsonResponse
    {
        $warning = PlayerWarning::findOrFail($id);
        $warning->update($request->validated());
        $warning->save();

        return response()->json($warning);
    }

    public function destroy(string $id): JsonResponse
    {
        $warning = PlayerWarning::findOrFail($id);
        $warning->delete();

        return response()->json();
    }
}
