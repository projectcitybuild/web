<?php

namespace App\Http\Controllers\Admin\Manage\Warnings;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\Admin\SortsAdminLists;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminPlayerWarningData;
use App\Models\MinecraftPlayer;
use App\Models\PlayerWarning;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlayerWarningController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;
    use SortsAdminLists;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::WARNINGS_VIEW);

        $sortableColumns = [
            'id' => 'id',
            'reason' => 'reason',
            'created_at' => 'created_at',
        ];
        $sortState = $this->adminListSortState($request, $sortableColumns);

        $warnings = function () use ($sortableColumns, $sortState) {
            $query = PlayerWarning::with('warnedPlayer', 'warnerPlayer');
            $this->applyAdminListSort($query, $sortableColumns, $sortState, 'created_at');

            return $query->paginate(50);
        };

        if (request()->wantsJson()) {
            return $warnings();
        }
        return $this->inertiaRender('Warnings/WarningList', [
            'warnings' => Inertia::defer(fn () => $warnings()->through(
                fn (PlayerWarning $warning): array => AdminPlayerWarningData::fromModel($warning)->toArray(),
            )),
            ...$sortState,
        ]);
    }

    public function create(Request $request)
    {
        $this->requires(WebManagePermission::WARNINGS_EDIT);

        return $this->inertiaRender('Warnings/WarningCreate');
    }

    public function store(Request $request)
    {
        $this->requires(WebManagePermission::WARNINGS_EDIT);

        $request->merge([
            'weight' => 1,
            'is_acknowledged' => false,
        ]);

        $validated = $request->validate([
            'warned_uuid' => ['required', new MinecraftUUIDRule],
            'warned_alias' => ['required', 'string'],
            'warner_uuid' => ['required', new MinecraftUUIDRule],
            'warner_alias' => ['required', 'string'],
            'weight' => ['required', 'numeric'],
            'reason' => ['required', 'string'],
            'additional_info' => ['nullable', 'string'],
            'created_at' => ['required', 'date'],
        ]);

        $warnedUuid = MinecraftUUID::tryParse($validated['warned_uuid']);
        $warnedPlayer = MinecraftPlayer::firstOrCreate($warnedUuid, alias: $validated['warned_alias']);
        $validated['warned_player_id'] = $warnedPlayer->id;

        $warnerUuid = MinecraftUUID::tryParse($validated['warner_uuid']);
        $warnerPlayer = MinecraftPlayer::firstOrCreate($warnerUuid, alias: $validated['warner_alias']);
        $validated['warner_player_id'] = $warnerPlayer->id;

        PlayerWarning::create($validated);

        return to_route('manage.warnings.index')
            ->with(['success' => 'Warning created successfully.']);
    }

    public function edit(PlayerWarning $warning)
    {
        $this->requires(WebManagePermission::WARNINGS_EDIT);

        $warning->load('warnedPlayer', 'warnerPlayer');

        return $this->inertiaRender('Warnings/WarningEdit', [
            'warning' => AdminPlayerWarningData::fromModel($warning),
        ]);
    }

    public function update(Request $request, PlayerWarning $warning)
    {
        $this->requires(WebManagePermission::WARNINGS_EDIT);

        $validated = $request->validate([
            'warned_uuid' => ['required', new MinecraftUUIDRule],
            'warned_alias' => ['required', 'string'],
            'warner_uuid' => ['required', new MinecraftUUIDRule],
            'warner_alias' => ['required', 'string'],
            'reason' => ['required', 'string'],
            'additional_info' => ['nullable', 'string'],
            'created_at' => ['required', 'date'],
        ]);

        $warnedUuid = MinecraftUUID::tryParse($validated['warned_uuid']);
        $warnedPlayer = MinecraftPlayer::firstOrCreate($warnedUuid, alias: $validated['warned_alias']);
        $validated['warned_player_id'] = $warnedPlayer->id;

        $warnerUuid = MinecraftUUID::tryParse($validated['warner_uuid']);
        $warnerPlayer = MinecraftPlayer::firstOrCreate($warnerUuid, alias: $validated['warner_alias']);
        $validated['warner_player_id'] = $warnerPlayer->id;

        $warning->update($validated);

        return to_route('manage.warnings.index')
            ->with(['success' => 'Warning updated successfully.']);
    }

    public function destroy(Request $request, PlayerWarning $warning)
    {
        $this->requires(WebManagePermission::WARNINGS_EDIT);

        $warning->delete();

        return to_route('manage.warnings.index')
            ->with(['success' => 'Warning deleted successfully.']);
    }
}
