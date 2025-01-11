<?php

namespace App\Http\Controllers\Manage\Minecraft;

use App\Domains\Manage\RendersManageApp;
use App\Domains\MinecraftEventBus\Events\MinecraftConfigUpdated;
use App\Http\Controllers\WebController;
use App\Models\MinecraftConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MinecraftConfigController extends WebController
{
    use RendersManageApp;

    public function create()
    {
        Gate::authorize('create', MinecraftConfig::class);

        $config = MinecraftConfig::byLatest()->first();

        return $this->inertiaRender('MinecraftConfig/MinecraftConfig',[
            'config' => $config,
        ]);
    }

    public function store(Request $request)
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
