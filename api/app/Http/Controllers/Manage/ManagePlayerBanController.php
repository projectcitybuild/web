<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerBanStoreRequest;
use App\Models\Eloquent\PlayerBan;
use Illuminate\Http\JsonResponse;

class ManagePlayerBanController extends Controller
{
    private const DEFAULT_RELATIONS = ['bannedPlayer', 'bannerPlayer', 'unbannerPlayer'];

    public function index(): JsonResponse
    {
        $bans = PlayerBan::with(self::DEFAULT_RELATIONS)
            ->cursorPaginate(config('api.page_size'));

        return response()->json($bans);
    }

    public function store(PlayerBanStoreRequest $request): JsonResponse
    {
        $ban = PlayerBan::create($request->validated());
        return response()->json($ban);
    }

    public function show(string $id): JsonResponse
    {
        $ban = PlayerBan::with(self::DEFAULT_RELATIONS)
            ->findOrFail($id);

        return response()->json($ban);
    }

    public function update(PlayerBanStoreRequest $request, string $id): JsonResponse
    {
        $ban = PlayerBan::findOrFail($id);
        $ban->update($request->validated());
        $ban->save();

        return response()->json($ban);
    }

    public function destroy(string $id): JsonResponse
    {
        $ban = PlayerBan::findOrFail($id);
        $ban->delete();

        return response()->json();
    }
}
