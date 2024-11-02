<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\WebController;
use App\Models\Badge;
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
            'list_hidden' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Badge::create([
            'display_name' => $request->get('display_name'),
            'unicode_icon' => $request->get('unicode_icon'),
            'list_hidden' => $request->get('list_hidden', 0),
        ]);

        return redirect(route('manage.badges.index'));
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
            'list_hidden' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $input = $request->all();
        if (! array_key_exists('list_hidden', $input)) {
            $input['list_hidden'] = 0;
        }
        $badge->update($input);
        $badge->save();

        return redirect(route('manage.badges.index'));
    }

    public function destroy(Request $request, int $badgeId): RedirectResponse
    {
        $badge = Badge::find($badgeId);
        $badge->delete();

        return redirect(route('manage.badges.index'));
    }
}
