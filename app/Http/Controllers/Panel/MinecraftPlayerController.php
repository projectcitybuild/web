<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Players\Models\MinecraftPlayer;
use App\Http\WebController;
use Illuminate\Http\Request;

class MinecraftPlayerController extends WebController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $minecraftPlayers = MinecraftPlayer::paginate(50);

        return view('admin.minecraft-player.index')->with(compact('minecraftPlayers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(MinecraftPlayer $minecraftPlayer)
    {
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
            'account_id' => 'nullable|exists:accounts'
        ]);

        $minecraftPlayer->update([
            'account_id' => $request->account_id
        ]);

        return redirect(route('front.panel.minecraft-players.show', $minecraftPlayer));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(MinecraftPlayer $minecraftPlayer)
    {
        //
    }
}
