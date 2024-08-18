<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Library\Mojang\Api\MojangPlayerApi;

class MinecraftPlayerController extends WebController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $minecraftPlayers = MinecraftPlayer::with(['account', 'aliases'])->paginate(50);

        return view('admin.minecraft-player.index')->with(compact('minecraftPlayers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.minecraft-player.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, MojangPlayerApi $api)
    {
        $request->validate([
            'uuid' => 'required',
            'account_id' => 'nullable|exists:accounts',
        ]);

        $uuid = str_replace('-', '', $request->uuid);

        if ($api->getNameHistoryOf($uuid) == null) {
            throw ValidationException::withMessages([
                'uuid' => 'Not an active Minecraft UUID',
            ]);
        }

        $mcPlayer = MinecraftPlayer::updateOrCreate(['uuid' => $uuid], ['account_id' => $request->account_id]);

        return redirect(route('front.panel.minecraft-players.show', $mcPlayer));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(MinecraftPlayer $minecraftPlayer)
    {
        $minecraftPlayer->load(['account', 'aliases', 'gamePlayerBans', 'gamePlayerBans.bannedPlayer.aliases', 'gamePlayerBans.bannerPlayer.aliases']);

        return view('admin.minecraft-player.show')->with(compact('minecraftPlayer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(MinecraftPlayer $minecraftPlayer)
    {
        return view('admin.minecraft-player.edit')->with(compact('minecraftPlayer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MinecraftPlayer $minecraftPlayer)
    {
        $request->validate([
            'account_id' => 'nullable|exists:accounts',
        ]);

        $minecraftPlayer->update([
            'account_id' => $request->account_id,
        ]);

        return redirect(route('front.panel.minecraft-players.show', $minecraftPlayer));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MinecraftPlayer $minecraftPlayer)
    {
        //
    }
}
