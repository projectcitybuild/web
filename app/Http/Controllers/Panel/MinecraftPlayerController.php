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
        $minecraftPlayers = MinecraftPlayer::all();

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Entities\Players\Models\MinecraftPlayer  $minecraftPlayer
     * @return \Illuminate\Http\Response
     */
    public function show(MinecraftPlayer $minecraftPlayer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Entities\Players\Models\MinecraftPlayer  $minecraftPlayer
     * @return \Illuminate\Http\Response
     */
    public function edit(MinecraftPlayer $minecraftPlayer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\Players\Models\MinecraftPlayer  $minecraftPlayer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MinecraftPlayer $minecraftPlayer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Entities\Players\Models\MinecraftPlayer  $minecraftPlayer
     * @return \Illuminate\Http\Response
     */
    public function destroy(MinecraftPlayer $minecraftPlayer)
    {
        //
    }
}
