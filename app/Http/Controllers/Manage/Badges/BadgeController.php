<?php

namespace App\Http\Controllers\Manage\Badges;

use App\Http\Controllers\WebController;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BadgeController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Badge::class);

        $badges = Badge::orderBy('display_name', 'desc')->paginate(100);

        return view('manage.pages.badges.index')
            ->with(compact('badges'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Badge::class);

        $badge = new Badge();

        return view('manage.pages.badges.create')
            ->with(compact('badge'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Badge::class);

        $request->validate([
            'display_name' => 'required|string',
            'unicode_icon' => 'required|string',
            'list_hidden' => 'boolean',
        ]);

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
    public function edit(Badge $badge)
    {
        Gate::authorize('update', $badge);

        return view('manage.pages.badges.edit')
            ->with(compact('badge'));
    }

    public function update(Request $request, Badge $badge)
    {
        Gate::authorize('update', $badge);

        $request->validate([
            'display_name' => 'required|string',
            'unicode_icon' => 'required|string',
            'list_hidden' => 'boolean',
        ]);

        $input = $request->all();
        if (! array_key_exists('list_hidden', $input)) {
            $input['list_hidden'] = 0;
        }
        $badge->update($input);
        $badge->save();

        return redirect(route('manage.badges.index'));
    }

    public function destroy(Request $request, Badge $badge)
    {
        Gate::authorize('delete', $badge);

        $badge->delete();

        return redirect(route('manage.badges.index'));
    }
}
