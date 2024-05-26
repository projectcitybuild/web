<?php

namespace App\Http\Controllers\Minecraft\Bans;

use App\Domains\Bans\Actions\CreateIPBan;
use App\Domains\Bans\Actions\CreateIPUnban;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bans\IPBanDeleteRequest;
use App\Http\Requests\Bans\IPBanStoreRequest;
use App\Models\IPBan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MinecraftIPBanController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $validated = $request->validate([
           'ip' => ['required', 'ip'],
        ]);
        $ban = IPBan::forIP($validated['ip'])->active()->firstOrFail();

        // TODO: update ban if it expired

        return response()->json($ban);
    }

    public function store(
        IPBanStoreRequest $request,
        CreateIPBan $createIPBan,
    ): JsonResponse
    {
        $ban = $createIPBan->call($request->transfer());
        return response()->json($ban);
    }

    public function delete(
        IPBanDeleteRequest $request,
        CreateIPUnban $createIPUnban,
    ): JsonResponse
    {
        $ban = $createIPUnban->call($request->transfer());
        return response()->json($ban);
    }
}
