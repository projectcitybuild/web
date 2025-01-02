<?php

namespace App\Http\Controllers\Manage\Badges;

use App\Http\Controllers\WebController;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class BadgeController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Badge::class);

        $badges = Badge::orderBy('created_at', 'desc')
            ->cursorPaginate(50);

        if ($request->wantsJson()) {
            return $badges;
        }
        return Inertia::render('Badges/BadgeList', compact('badges'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Badge::class);

        return Inertia::render('Badges/BadgeCreate');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Badge::class);

        $request->validate([
            'display_name' => 'required|string',
            'unicode_icon' => 'required|string',
            'list_hidden' => 'nullable|boolean',
        ]);

        Badge::create([
            'display_name' => $request->get('display_name'),
            'unicode_icon' => $request->get('unicode_icon'),
            'list_hidden' => $request->get('list_hidden', false),
        ]);

        return to_route('manage.badges.index')
            ->with(['success' => 'Badge created successfully.']);
    }

    public function edit(Badge $badge)
    {
        Gate::authorize('update', $badge);

        return Inertia::render('Badges/BadgeEdit', compact('badge'));
    }

    public function update(Request $request, Badge $badge)
    {
        Gate::authorize('update', $badge);

        $validated = $request->validate([
            'display_name' => 'required|string',
            'unicode_icon' => 'required|string',
            'list_hidden' => 'nullable|boolean',
        ]);

        $validated['list_hidden'] = $validated['list_hidden'] ?? false;

        $badge->update($validated);

        return to_route('manage.badges.index')
            ->with(['success' => 'Badge updated successfully.']);
    }

    public function destroy(Request $request, Badge $badge)
    {
        Gate::authorize('delete', $badge);

        $badge->delete();

        return to_route('manage.badges.index')
            ->with(['success' => 'Badge deleted successfully.']);
    }
}
