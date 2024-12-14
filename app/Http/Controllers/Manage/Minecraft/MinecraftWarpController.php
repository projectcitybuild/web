<?php

namespace App\Http\Controllers\Manage\Minecraft;

use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Http\Controllers\WebController;
use App\Models\MinecraftWarp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class MinecraftWarpController extends WebController
{
    use ValidatesCoordinates;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', MinecraftWarp::class);

        $warps = MinecraftWarp::orderBy('name', 'asc')
            ->paginate(100);

        return view('manage.pages.minecraft-warps.index')
            ->with(compact('warps'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', MinecraftWarp::class);

        $warp = new MinecraftWarp();

        return view('manage.pages.minecraft-warps.create')
            ->with(compact('warp'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', MinecraftWarp::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique('minecraft_warps')],
            ...$this->coordinateRules,
        ]);

        MinecraftWarp::create($validated);

        return redirect(route('manage.minecraft.warps.index'));
    }

    public function edit(MinecraftWarp $warp)
    {
        Gate::authorize('update', $warp);

        return view('manage.pages.minecraft-warps.edit')
            ->with(compact('warp'));
    }

    public function update(Request $request, MinecraftWarp $warp)
    {
        Gate::authorize('update', $warp);

        $validated = $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique('minecraft_warps')->ignore($warp)],
            ...$this->coordinateRules,
        ]);

        $warp->update($validated);

        return redirect(route('manage.minecraft.warps.index'));
    }

    public function destroy(Request $request, MinecraftWarp $warp)
    {
        Gate::authorize('delete', $warp);

        $warp->delete();

        return redirect(route('manage.minecraft.warps.index'));
    }
}
