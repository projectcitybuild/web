<?php

namespace App\Http\Controllers\Manage\Minecraft;

use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Http\Controllers\WebController;
use App\Models\MinecraftBuild;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MinecraftBuildController extends WebController
{
    use ValidatesCoordinates;

    public function index(Request $request)
    {
        $builds = MinecraftBuild::orderBy('name', 'asc')
            ->with('player')
            ->paginate(100);

        return view('manage.pages.minecraft-builds.index')
            ->with(compact('builds'));
    }

    public function create(Request $request)
    {
        $build = new MinecraftBuild();

        return view('manage.pages.minecraft-builds.create')
            ->with(compact('build'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'alpha_dash', Rule::unique(MinecraftBuild::tableName())],
            ...$this->coordinateRules,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        MinecraftBuild::create($validator->validated());

        return redirect(route('manage.minecraft.builds.index'));
    }

    public function edit(MinecraftBuild $build)
    {
        return view('manage.pages.minecraft-builds.edit')
            ->with(compact('build'));
    }

    public function update(Request $request, MinecraftBuild $build)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'alpha_dash', Rule::unique(MinecraftBuild::tableName())->ignore($build)],
            ...$this->coordinateRules,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $build->update($validator->validated());

        return redirect(route('manage.minecraft.builds.index'));
    }

    public function destroy(Request $request, MinecraftBuild $build)
    {
        $build->delete();

        return redirect(route('manage.minecraft.builds.index'));
    }
}
