<?php

namespace App\Http\Controllers\Admin\Manage\Minecraft;

use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\Admin\SortsAdminLists;
use App\Http\Controllers\WebController;
use App\Http\Data\MinecraftWarpData;
use App\Models\MinecraftWarp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MinecraftWarpController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;
    use SortsAdminLists;
    use ValidatesCoordinates;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::WARPS_VIEW);

        $sortableColumns = [
            'id' => 'id',
            'name' => 'name',
            'world' => 'world',
            'x' => 'x',
            'y' => 'y',
            'z' => 'z',
            'pitch' => 'pitch',
            'yaw' => 'yaw',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];
        $sortState = $this->adminListSortState($request, $sortableColumns);

        $warps = function () use ($sortableColumns, $sortState) {
            $query = MinecraftWarp::query();
            $this->applyAdminListSort($query, $sortableColumns, $sortState, 'created_at');

            return $query->paginate(50);
        };

        if (request()->wantsJson()) {
            return $warps();
        }
        return $this->inertiaRender('Warps/WarpList', [
            'warps' => Inertia::defer(fn () => $warps()->through(
                fn (MinecraftWarp $warp): array => MinecraftWarpData::fromModel($warp)->toArray(),
            )),
            ...$sortState,
        ]);
    }

    public function create(Request $request)
    {
        $this->requires(WebManagePermission::WARPS_EDIT);

        return $this->inertiaRender('Warps/WarpCreate');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->requires(WebManagePermission::WARPS_EDIT);

        $validated = $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique('minecraft_warps')],
            ...$this->coordinateRules,
        ]);

        MinecraftWarp::create($validated);

        return to_route('manage.minecraft.warps.index')
            ->with(['success' => 'Warp created successfully.']);
    }

    public function edit(MinecraftWarp $warp)
    {
        $this->requires(WebManagePermission::WARPS_EDIT);

        return $this->inertiaRender('Warps/WarpEdit', [
            'warp' => MinecraftWarpData::fromModel($warp),
        ]);
    }

    public function update(Request $request, MinecraftWarp $warp)
    {
        $this->requires(WebManagePermission::WARPS_EDIT);

        $validated = $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique('minecraft_warps')->ignore($warp)],
            ...$this->coordinateRules,
        ]);

        $warp->update($validated);

        return to_route('manage.minecraft.warps.index')
            ->with(['success' => 'Warp updated successfully.']);
    }

    public function destroy(Request $request, MinecraftWarp $warp)
    {
        $this->requires(WebManagePermission::WARPS_EDIT);

        $warp->delete();

        return to_route('manage.minecraft.warps.index')
            ->with(['success' => 'Warp '.$warp->name.' deleted successfully.']);
    }
}
