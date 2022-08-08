<?php

namespace App\Http\Controllers\Panel;

use App\Http\WebController;
use Entities\Models\Eloquent\Badge;
use Entities\Models\Eloquent\Server;
use Entities\Models\Eloquent\ServerCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BadgeController extends WebController
{
    public function index(Request $request): Badge|Factory|View
    {
        $badges = Badge::orderBy('display_name', 'desc')->paginate(100);

        return view('admin.badges.index')->with(compact('badges'));
    }

    public function create(Request $request): Application|Factory|View
    {
        $badge = new Badge();

        return view('admin.badges.create')
            ->with(compact('badge'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string',
            'unicode_icon' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Badge::create([
            'display_name' => $request->get('display_name'),
            'unicode_icon' => $request->get('unicode_icon'),
        ]);

        return redirect(route('front.panel.badges.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $badgeId): Badge|Factory|View
    {
        $badge = Badge::find($badgeId);

        return view('admin.badges.edit')->with(compact('badge'));
    }

    public function update(Request $request, int $badgeId): RedirectResponse
    {
        $badge = Badge::find($badgeId);

        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string',
            'unicode_icon' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $badge->update($request->all());
        $badge->save();

        return redirect(route('front.panel.badges.index'));
    }

    public function destroy(Request $request, int $badgeId): RedirectResponse
    {
        $badge = Badge::find($badgeId);
        $badge->delete();

        return redirect(route('front.panel.badges.index'));
    }
}
