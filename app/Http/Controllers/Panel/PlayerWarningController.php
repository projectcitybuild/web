<?php

namespace App\Http\Controllers\Panel;

use App\Http\WebController;
use Entities\Models\Eloquent\Badge;
use Entities\Models\Eloquent\PlayerWarning;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlayerWarningController extends WebController
{
    public function index(Request $request): View
    {
        $warnings = PlayerWarning::with('warnedPlayer', 'warnerPlayer')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('admin.warnings.index')
            ->with(compact('warnings'));
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
            'weight' => 'required|integer',
            'is_acknowledged' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Badge::create([
            'display_name' => $request->get('display_name'),
            'unicode_icon' => $request->get('unicode_icon'),
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
            'weight' => 'required|integer',
            'is_acknowledged' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        PlayerWarning::find($warningId)->update($request->all());

        return redirect(route('front.panel.warnings.index'));
    }

    public function destroy(Request $request, int $warningId): RedirectResponse
    {
        PlayerWarning::find($warningId)->delete();

        return redirect(route('front.panel.warnings.index'));
    }
}
