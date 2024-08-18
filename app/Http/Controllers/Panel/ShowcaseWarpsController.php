<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\WebController;
use App\Models\ShowcaseWarp;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShowcaseWarpsController extends WebController
{
    public function index(Request $request): ShowcaseWarp|Factory|View
    {
        $warps = ShowcaseWarp::orderBy('name', 'asc')
            ->paginate(100);

        return view('admin.showcase-warps.index')->with(compact('warps'));
    }

    public function create(Request $request): Application|Factory|View
    {
        $warp = new ShowcaseWarp();

        return view('admin.showcase-warps.create')
            ->with(compact('warp'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|alpha_dash',
            'title' => 'string',
            'description' => 'string',
            'creators' => 'string',
            'location_world' => 'required|string',
            'location_x' => 'required|integer',
            'location_y' => 'required|integer',
            'location_z' => 'required|integer',
            'location_pitch' => 'required|numeric',
            'location_yaw' => 'required|numeric',
            'built_at' => 'date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ShowcaseWarp::create($request->all());

        return redirect(route('front.panel.showcase-warps.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): ShowcaseWarp|Factory|View
    {
        $warp = ShowcaseWarp::find($id);

        return view('admin.showcase-warps.edit')->with(compact('warp'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $warp = ShowcaseWarp::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|alpha_dash',
            'title' => 'string',
            'description' => 'string',
            'creators' => 'string',
            'location_world' => 'required|string',
            'location_x' => 'required|integer',
            'location_y' => 'required|integer',
            'location_z' => 'required|integer',
            'location_pitch' => 'required|numeric',
            'location_yaw' => 'required|numeric',
            'built_at' => 'date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $warp->update($request->all());
        $warp->save();

        return redirect(route('front.panel.showcase-warps.index'));
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $warp = ShowcaseWarp::find($id);
        $warp->delete();

        return redirect(route('front.panel.showcase-warps.index'));
    }
}
