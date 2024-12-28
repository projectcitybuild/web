<?php

namespace App\Http\Controllers\Manage\Minecraft;

use App\Domains\MinecraftEventBus\Events\MinecraftConfigUpdated;
use App\Http\Controllers\WebController;
use App\Models\MinecraftConfig;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class MinecraftConfigController extends WebController
{
    public function create()
    {
        Gate::authorize('create', MinecraftConfig::class);

        $config = MinecraftConfig::byLatest()->first();

        return Inertia::render('MinecraftConfig/MinecraftConfig',[
            'config' => $config,
        ]);
    }

    public function update(Request $request, MinecraftPlayer $minecraftPlayer)
    {
        Gate::authorize('update', MinecraftConfig::class);

        $validated = $request->validate([
            'config' => 'required|json',
        ]);

        $latest = MinecraftConfig::byLatest()->first();
        $config = MinecraftConfig::create([
            'config' => json_decode($validated['config']),
            'version' => $latest->version + 1,
        ]);

        MinecraftConfigUpdated::dispatch($config);

        return to_route('manage.minecraft.config.create', $config)
            ->with(['success' => 'Config successfully updated']);
    }
}
