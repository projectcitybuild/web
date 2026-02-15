<?php

namespace App\Http\Controllers\Manage\Minecraft;

use App\Domains\MinecraftEventBus\Events\MinecraftConfigUpdated;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\MinecraftConfig;
use Illuminate\Http\Request;

class MinecraftConfigController extends WebController
{
    use RendersManageApp;
    use AuthorizesPermissions;

    public function create()
    {
        $this->requires(WebManagePermission::REMOTE_CONFIG_EDIT);

        $config = MinecraftConfig::byLatest()->first();

        return $this->inertiaRender('MinecraftConfig/MinecraftConfig', [
            'config' => $config,
        ]);
    }

    public function store(Request $request)
    {
        $this->requires(WebManagePermission::REMOTE_CONFIG_EDIT);

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
