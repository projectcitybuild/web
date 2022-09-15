<?php

namespace App\Http\Controllers\Panel;

use App\Http\WebController;
use Entities\Models\Eloquent\GameIPBan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GameIPBanController extends WebController
{
    public function index(Request $request): View
    {
        $bans = GameIPBan::with('bannerPlayer')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('admin.ip-bans.index')
            ->with(compact('bans'));
    }

    public function create(Request $request): View
    {
        $ban = new GameIPBan();

        return view('admin.ip-bans.create')
            ->with(compact('ban'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'banner_player_id' => 'required|max:60',
            'ip_address' => 'required|ip',
            'reason' => 'required|string',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        GameIPBan::create([
            'banner_player_id' => $request->get('banner_player_id'),
            'ip_address' => $request->get('ip_address'),
            'reason' => $request->get('reason'),
            'created_at' => $request->get('created_at'),
            'updated_at' => $request->get('updated_at'),
        ]);

        return redirect(route('front.panel.ip-bans.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $banId): View
    {
        $ban = GameIPBan::find($banId);

        return view('admin.ip-bans.edit')
            ->with(compact('ban'));
    }

    public function update(Request $request, int $banId): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'banner_player_id' => 'required|max:60',
            'ip_address' => 'required|ip',
            'reason' => 'required|string',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        GameIPBan::find($banId)->update($request->all());

        return redirect(route('front.panel.ip-bans.index'));
    }

    public function destroy(Request $request, int $banId): RedirectResponse
    {
        GameIPBan::find($banId)->delete();

        return redirect(route('front.panel.ip-bans.index'));
    }
}
