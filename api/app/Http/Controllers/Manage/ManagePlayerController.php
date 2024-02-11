<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\ManagePlayerRequest;
use App\Models\Eloquent\Player;
use Illuminate\Http\JsonResponse;

class ManagePlayerController extends Controller
{
    public function index(): JsonResponse
    {
        $players = Player::cursorPaginate(config('api.page_size'));

        return response()->json($players);
    }

    public function store(ManagePlayerRequest $request): JsonResponse
    {
        $player = Player::create($request->validated());

        return response()->json($player);
    }

    public function show(string $id): JsonResponse
    {
        $player = Player::findOrFail($id);

        return response()->json($player);
    }

    public function update(ManagePlayerRequest $request, string $id): JsonResponse
    {
        $player = Player::findOrFail($id);
        $player->update($request->validated());
        $player->save();

        return response()->json($player);
    }

    public function destroy(string $id): JsonResponse
    {
        $player = Player::findOrFail($id);
        $player->delete();

        return response()->json();
    }
}
