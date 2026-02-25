<?php

namespace App\Http\Controllers\Manage\Homes;

use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\MinecraftHome;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class HomeController extends WebController
{
    use AuthorizesPermissions;
    use RendersManageApp;
    use ValidatesCoordinates;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::HOMES_VIEW);

        $homes = function () {
            return MinecraftHome::with('player')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
        };

        if (request()->wantsJson()) {
            return $homes();
        }
        return $this->inertiaRender('Homes/HomeList', [
            'homes' => Inertia::defer($homes),
        ]);
    }

    public function create(Request $request)
    {
        $this->requires(WebManagePermission::HOMES_EDIT);

        return $this->inertiaRender('Homes/HomeCreate');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->requires(WebManagePermission::HOMES_EDIT);

        $validated = $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique(MinecraftHome::tableName())],
            ...$this->coordinateRules,
        ]);

        MinecraftHome::create($validated);

        return to_route('manage.homes.index')
            ->with(['success' => 'Home created successfully.']);
    }

    public function edit(MinecraftHome $home)
    {
        $this->requires(WebManagePermission::HOMES_EDIT);

        $home->load('player');

        return $this->inertiaRender('Homes/HomeEdit', compact('home'));
    }

    public function update(Request $request, MinecraftHome $home)
    {
        $this->requires(WebManagePermission::HOMES_EDIT);

        $validated = $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique(MinecraftHome::tableName())->ignore($home)],
            ...$this->coordinateRules,
        ]);

        $home->update($validated);

        return to_route('manage.homes.index')
            ->with(['success' => 'Home updated successfully.']);
    }

    public function destroy(Request $request, MinecraftHome $home)
    {
        $this->requires(WebManagePermission::HOMES_EDIT);

        $home->delete();

        return to_route('manage.homes.index')
            ->with(['success' => 'Home '.$home->name.' deleted successfully.']);
    }
}
