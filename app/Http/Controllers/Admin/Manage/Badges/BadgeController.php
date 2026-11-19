<?php

namespace App\Http\Controllers\Admin\Manage\Badges;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\Admin\SortsAdminLists;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminAccountBadgeData;
use App\Models\Badge;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BadgeController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;
    use SortsAdminLists;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::BADGES_VIEW);

        $sortableColumns = [
            'id' => 'id',
            'display_name' => 'display_name',
            'unicode_icon' => 'unicode_icon',
            'list_hidden' => 'list_hidden',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];
        $sortState = $this->adminListSortState($request, $sortableColumns);

        $badges = function () use ($sortableColumns, $sortState) {
            $query = Badge::query();
            $this->applyAdminListSort($query, $sortableColumns, $sortState, 'created_at');

            return $query->paginate(50);
        };

        if ($request->wantsJson()) {
            return $badges();
        }
        return $this->inertiaRender('Badges/BadgeList', [
            'badges' => Inertia::defer(fn () => $badges()->through(
                fn (Badge $badge): array => AdminAccountBadgeData::fromModel($badge)->toArray(),
            )),
            ...$sortState,
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

        return $this->inertiaRender('Badges/BadgeEdit', [
            'badge' => AdminAccountBadgeData::fromModel($badge),
        ]);
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
