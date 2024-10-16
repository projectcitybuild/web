<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\WebController;
use App\Models\PlayerWarning;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PlayerWarningController extends WebController
{
    public function index(Request $request): View
    {
        $warnings = PlayerWarning::with('warnedPlayer.aliases', 'warnerPlayer.aliases')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('admin.warnings.index')
            ->with(compact('warnings'));
    }

    public function show(PlayerWarning $warning): View
    {
        return view('admin.warnings.show')
            ->with(compact('warning'));
    }

    public function create(Request $request): View
    {
        $warning = new PlayerWarning();

        return view('admin.warnings.create')
            ->with(compact('warning'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'warned_player_id' => 'required|max:60',
            'warner_player_id' => 'required|max:60',
            'reason' => 'required|string',
            'additional_info' => 'string|nullable',
            'is_acknowledged' => 'boolean',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
            'acknowledged_at' => ['date', 'nullable', Rule::requiredIf($request->has('is_acknowledged'))],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        PlayerWarning::create([
            'warned_player_id' => $request->get('warned_player_id'),
            'warner_player_id' => $request->get('warner_player_id'),
            'reason' => $request->get('reason'),
            'additional_info' => $request->get('additional_info'),
            'weight' => 1,
            'is_acknowledged' => $request->get('is_acknowledged', false),
            'created_at' => $request->get('created_at'),
            'updated_at' => $request->get('updated_at'),
            'acknowledged_at' => $request->get('acknowledged_at'),
        ]);

        return redirect(route('front.panel.warnings.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $warningId): View
    {
        $warning = PlayerWarning::find($warningId);

        return view('admin.warnings.edit')
            ->with(compact('warning'));
    }

    public function update(Request $request, int $warningId): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'warned_player_id' => 'required|max:60',
            'warner_player_id' => 'required|max:60',
            'reason' => 'required|string',
            'additional_info' => 'string|nullable',
            'is_acknowledged' => 'boolean',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
            'acknowledged_at' => ['date', 'nullable', Rule::requiredIf($request->has('is_acknowledged'))],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        PlayerWarning::find($warningId)->update(
            array_merge($request->all(), [
                'is_acknowledged' => $request->get('is_acknowledged', false),
            ]),
        );

        return redirect(route('front.panel.warnings.index'));
    }

    public function destroy(Request $request, int $warningId): RedirectResponse
    {
        PlayerWarning::find($warningId)->delete();

        return redirect(route('front.panel.warnings.index'));
    }
}
