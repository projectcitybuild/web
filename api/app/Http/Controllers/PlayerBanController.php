<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlayerBanStoreRequest;
use App\Models\Eloquent\PlayerBan;
use Illuminate\Http\JsonResponse;

class PlayerBanController extends Controller
{
    public function index(): JsonResponse
    {
        $bans = PlayerBan::get();

        return response()->json($bans);
    }

    public function store(PlayerBanStoreRequest $request): JsonResponse
    {
        $ban = PlayerBan::create($request->validated());
        return response()->json($ban);
    }

    public function show(string $id): JsonResponse
    {
        $ban = PlayerBan::findOrFail($id);

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
