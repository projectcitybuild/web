<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\ManagePlayerBanStoreRequest;
use App\Models\IPBan;
use Illuminate\Http\JsonResponse;

class ManageIPBanController extends Controller
{
    private const DEFAULT_RELATIONS = ['bannerPlayer', 'unbannerPlayer'];

    public function index(): JsonResponse
    {
        $bans = IPBan::with(self::DEFAULT_RELATIONS)
            ->cursorPaginate(config('api.page_size'));

        return response()->json($bans);
    }

    public function store(ManagePlayerBanStoreRequest $request): JsonResponse
    {
        $ban = IPBan::create($request->validated());
        return response()->json($ban);
    }

    public function show(string $id): JsonResponse
    {
        $ban = IPBan::with(self::DEFAULT_RELATIONS)
            ->findOrFail($id);

        return response()->json($ban);
    }

    public function update(ManagePlayerBanStoreRequest $request, string $id): JsonResponse
    {
        $ban = IPBan::findOrFail($id);
        $ban->update($request->validated());
        $ban->save();

        return response()->json($ban);
    }

    public function destroy(string $id): JsonResponse
    {
        $ban = IPBan::findOrFail($id);
        $ban->delete();

        return response()->json();
    }
}
