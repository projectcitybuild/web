<?php

namespace App\Http\Controllers\Manage\Badges;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Badge;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BadgeController extends WebController
{
    use AuthorizesPermissions;
    use RendersManageApp;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::BADGES_VIEW);

        $badges = function () {
            return Badge::orderBy('created_at', 'desc')
                ->paginate(50);
        };

        if ($request->wantsJson()) {
            return $badges();
        }
        return $this->inertiaRender('Badges/BadgeList', [
            'badges' => Inertia::defer($badges),
        ]);
    }

    public function create(Request $request)
    {
        $this->requires(WebManagePermission::BADGES_EDIT);

        return $this->inertiaRender('Badges/BadgeCreate');
    }

    public function store(Request $request)
    {
        $this->requires(WebManagePermission::BADGES_EDIT);

        $request->merge([
            'list_hidden' => $request->has('list_hidden'),
        ]);
        $request->validate([
            'display_name' => ['required', 'string'],
            'unicode_icon' => ['required', 'string'],
            'list_hidden' => ['boolean'],
        ]);

        Badge::create([
            'display_name' => $request->request->get('display_name'),
            'unicode_icon' => $request->request->get('unicode_icon'),
            'list_hidden' => $request->request->get('list_hidden', false),
        ]);

        return to_route('manage.badges.index')
            ->with(['success' => 'Badge created successfully.']);
    }

    public function edit(Badge $badge)
    {
        $this->requires(WebManagePermission::BADGES_EDIT);

        return $this->inertiaRender('Badges/BadgeEdit', compact('badge'));
    }

    public function update(Request $request, Badge $badge)
    {
        $this->requires(WebManagePermission::BADGES_EDIT);

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
        $this->requires(WebManagePermission::BADGES_EDIT);

        $badge->delete();

        return to_route('manage.badges.index')
            ->with(['success' => 'Badge deleted successfully.']);
    }
}
