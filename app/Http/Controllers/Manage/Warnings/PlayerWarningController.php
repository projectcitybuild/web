<?php

namespace App\Http\Controllers\Manage\Warnings;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;
use App\Models\PlayerWarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PlayerWarningController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', PlayerWarning::class);

        $warnings = PlayerWarning::with('warnedPlayer', 'warnerPlayer')
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(50);

        if (request()->wantsJson()) {
            return $warnings;
        }
        return Inertia::render('Warnings/WarningList', compact('warnings'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', PlayerWarning::class);

        return Inertia::render('Warnings/WarningCreate');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', PlayerWarning::class);

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
        $validated['warned_player_id'] = $warnedPlayer->getKey();

        $warnerUuid = MinecraftUUID::tryParse($validated['warner_uuid']);
        $warnerPlayer = MinecraftPlayer::firstOrCreate($warnerUuid, alias: $validated['warner_alias']);
        $validated['warner_player_id'] = $warnerPlayer->getKey();

        PlayerWarning::create($validated);

        return to_route('manage.warnings.index')
            ->with(['success' => 'Warning created successfully.']);
    }

    public function edit(PlayerWarning $warning)
    {
        Gate::authorize('update', $warning);

        $warning->load('warnedPlayer', 'warnerPlayer');

        return INertia::render('Warnings/WarningEdit', compact('warning'));
    }

    public function update(Request $request, PlayerWarning $warning)
    {
        Gate::authorize('update', $warning);

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
        $validated['warned_player_id'] = $warnedPlayer->getKey();

        $warnerUuid = MinecraftUUID::tryParse($validated['warner_uuid']);
        $warnerPlayer = MinecraftPlayer::firstOrCreate($warnerUuid, alias: $validated['warner_alias']);
        $validated['warner_player_id'] = $warnerPlayer->getKey();

        $warning->update($validated);

        return to_route('manage.warnings.index')
            ->with(['success' => 'Warning updated successfully.']);
    }

    public function destroy(Request $request, PlayerWarning $warning)
    {
        Gate::authorize('delete', $warning);

        $warning->delete();

        return to_route('manage.warnings.index')
            ->with(['success' => 'Warning deleted successfully.']);
    }
}
