<?php

namespace App\Http\Controllers\Manage\Minecraft;

use App\Domains\MinecraftEventBus\Events\MinecraftConfigUpdated;
use App\Http\Controllers\WebController;
use App\Models\MinecraftConfig;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;

class MinecraftConfigController extends WebController
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $config = MinecraftConfig::byLatest()->first();

        return view('manage.pages.minecraft-config.create', ['config' => $config]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MinecraftPlayer $minecraftPlayer)
    {
        $input = $request->validate([
            'config' => 'required|json',
        ]);

        $latest = MinecraftConfig::byLatest()->first();
        $config = MinecraftConfig::create([
            'config' => json_decode($input['config']),
            'version' => $latest->version + 1,
        ]);

        MinecraftConfigUpdated::dispatch($config);

        return redirect(route('manage.minecraft.config.create', $config));
    }
}
