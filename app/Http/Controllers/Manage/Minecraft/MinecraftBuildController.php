<?php

namespace App\Http\Controllers\Manage\Minecraft;

use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Http\Controllers\WebController;
use App\Models\MinecraftBuild;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class MinecraftBuildController extends WebController
{
    use ValidatesCoordinates;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', MinecraftBuild::class);

        $builds = MinecraftBuild::orderBy('name', 'asc')
            ->with('player')
            ->paginate(100);

        return view('manage.pages.minecraft-builds.index')
            ->with(compact('builds'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', MinecraftBuild::class);

        $build = new MinecraftBuild;

        return view('manage.pages.minecraft-builds.create')
            ->with(compact('build'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', MinecraftBuild::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique(MinecraftBuild::tableName())],
            ...$this->coordinateRules,
        ]);

        MinecraftBuild::create($validated);

        return redirect(route('manage.minecraft.builds.index'));
    }

    public function edit(MinecraftBuild $build)
    {
        Gate::authorize('update', $build);

        return view('manage.pages.minecraft-builds.edit')
            ->with(compact('build'));
    }

    public function update(Request $request, MinecraftBuild $build)
    {
        Gate::authorize('update', $build);

        $validated = $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique(MinecraftBuild::tableName())->ignore($build)],
            ...$this->coordinateRules,
        ]);

        $build->update($validated);

        return redirect(route('manage.minecraft.builds.index'));
    }

    public function destroy(Request $request, MinecraftBuild $build)
    {
        Gate::authorize('delete', $build);

        $build->delete();

        return redirect(route('manage.minecraft.builds.index'));
    }
}
