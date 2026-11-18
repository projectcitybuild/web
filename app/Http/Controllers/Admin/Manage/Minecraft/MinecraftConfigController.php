<?php

namespace App\Http\Controllers\Admin\Manage\Minecraft;

use App\Domains\MinecraftEventBus\Events\MinecraftConfigUpdated;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use App\Http\Data\MinecraftConfigPageData;
use App\Models\MinecraftConfig;
use Illuminate\Http\Request;
use Inertia\Response;

class MinecraftConfigController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;

    public function create(): Response
    {
        $this->requires(WebManagePermission::REMOTE_CONFIG_EDIT);

        $config = MinecraftConfig::byLatest()->firstOrFail();

        return $this->inertiaRender('MinecraftConfig/MinecraftConfig', [
            'config' => (new MinecraftConfigPageData(
                version: $config->version,
                config: $config->config,
                created_at: $config->created_at->toISOString(),
                updated_at: $config->updated_at->toISOString(),
            ))->toArray(),
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
