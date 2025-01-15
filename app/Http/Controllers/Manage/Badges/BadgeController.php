<?php

namespace App\Http\Controllers\Manage\Badges;

use App\Domains\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class BadgeController extends WebController
{
    use RendersManageApp;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Badge::class);

        $badges = function () {
            return Badge::orderBy('created_at', 'desc')
                ->cursorPaginate(50);
        };

        if ($request->wantsJson()) {
            return $badges();
        }
        return $this->inertiaRender('Badges/BadgeList', [
            'badges' => Inertia::defer($badges)
        ]);
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Badge::class);

        return $this->inertiaRender('Badges/BadgeCreate');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Badge::class);

        $request->merge([
            'list_hidden' => $request->has('list_hidden'),
        ]);
        $request->validate([
            'display_name' => ['required', 'string'],
            'unicode_icon' => ['required', 'string'],
            'list_hidden' => ['boolean'],
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

        return $this->inertiaRender('Badges/BadgeEdit', compact('badge'));
    }

    public function update(Request $request, Badge $badge)
    {
        Gate::authorize('update', $badge);

        $request->merge([
            'list_hidden' => $request->has('list_hidden'),
        ]);
        $validated = $request->validate([
            'display_name' => ['required', 'string'],
            'unicode_icon' => ['required', 'string'],
            'list_hidden' => ['boolean'],
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
