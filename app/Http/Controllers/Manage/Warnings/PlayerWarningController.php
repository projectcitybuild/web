<?php

namespace App\Http\Controllers\Manage\Warnings;

use App\Http\Controllers\WebController;
use App\Models\PlayerWarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PlayerWarningController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', PlayerWarning::class);

        $warnings = PlayerWarning::with('warnedPlayer', 'warnerPlayer')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('manage.pages.warnings.index')
            ->with(compact('warnings'));
    }

    public function show(PlayerWarning $warning)
    {
        Gate::authorize('view', $warning);

        return view('manage.pages.warnings.show')
            ->with(compact('warning'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', PlayerWarning::class);

        $warning = new PlayerWarning();

        return view('manage.pages.warnings.create')
            ->with(compact('warning'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', PlayerWarning::class);

        $request->merge([
            'weight' => 1,
            'is_acknowledged' => $request->has('is_acknowledged'),
        ]);

        $validated = $request->validate([
            'warned_player_id' => 'required|max:60',
            'warner_player_id' => 'required|max:60',
            'reason' => 'required|string',
            'additional_info' => 'string|nullable',
            'is_acknowledged' => 'boolean',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
            'acknowledged_at' => ['date', 'nullable', Rule::requiredIf($request->has('is_acknowledged'))],
        ]);

        PlayerWarning::create($validated);

        return redirect(route('manage.warnings.index'));
    }

    public function edit(PlayerWarning $warning)
    {
        Gate::authorize('update', $warning);

        return view('manage.pages.warnings.edit')
            ->with(compact('warning'));
    }

    public function update(Request $request, PlayerWarning $warning)
    {
        Gate::authorize('update', $warning);

        $request->merge([
            'weight' => 1,
            'is_acknowledged' => $request->has('is_acknowledged'),
        ]);

        $validated = $request->validate([
            'warned_player_id' => 'required|max:60',
            'warner_player_id' => 'required|max:60',
            'reason' => 'required|string',
            'additional_info' => 'string|nullable',
            'is_acknowledged' => 'boolean',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
            'acknowledged_at' => ['date', 'nullable', Rule::requiredIf($request->has('is_acknowledged'))],
        ]);

        $warning->update($validated);

        return redirect(route('manage.warnings.index'));
    }

    public function destroy(Request $request, PlayerWarning $warning)
    {
        Gate::authorize('delete', $warning);

        $warning->delete();

        return redirect(route('manage.warnings.index'));
    }
}
