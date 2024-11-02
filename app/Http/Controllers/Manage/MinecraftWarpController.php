<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\WebController;
use App\Models\MinecraftWarp;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MinecraftWarpController extends WebController
{
    public function index(Request $request): MinecraftWarp|Factory|View
    {
        $warps = MinecraftWarp::orderBy('name', 'asc')
            ->paginate(100);

        return view('manage.pages.minecraft-warps.index')->with(compact('warps'));
    }

    public function create(Request $request): Application|Factory|View
    {
        $warp = new MinecraftWarp();

        return view('manage.pages.minecraft-warps.create')
            ->with(compact('warp'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'alpha_dash', Rule::unique('minecraft_warps')],
            'world' => 'required|string',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'z' => 'required|numeric',
            'pitch' => 'required|numeric',
            'yaw' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        MinecraftWarp::create($request->all());

        return redirect(route('manage.minecraft.warps.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MinecraftWarp $warp): MinecraftWarp|Factory|View
    {
        return view('manage.pages.minecraft-warps.edit')
            ->with(compact('warp'));
    }

    public function update(Request $request, MinecraftWarp $warp): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'alpha_dash', Rule::unique('minecraft_warps')->ignore($warp)],
            'world' => 'required|string',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'z' => 'required|numeric',
            'pitch' => 'required|numeric',
            'yaw' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $warp->update($request->all());

        return redirect(route('manage.minecraft.warps.index'));
    }

    public function destroy(Request $request, MinecraftWarp $warp): RedirectResponse
    {
        $warp->delete();

        return redirect(route('manage.minecraft.warps.index'));
    }
}
