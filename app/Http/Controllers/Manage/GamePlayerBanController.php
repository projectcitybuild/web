<?php

namespace App\Http\Controllers\Manage;

use App\Domains\Bans\Data\UnbanType;
use App\Domains\MinecraftEventBus\Events\MinecraftUuidBanned;
use App\Http\Controllers\WebController;
use App\Models\GamePlayerBan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GamePlayerBanController extends WebController
{
    public function index(Request $request): View
    {
        $bans = GamePlayerBan::with('bannedPlayer', 'bannerPlayer', 'unbannerPlayer')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('manage.player-bans.index')
            ->with(compact('bans'));
    }

    public function create(Request $request): View
    {
        $ban = new GamePlayerBan();

        return view('manage.player-bans.create')
            ->with(compact('ban'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'banned_player_id' => 'required|max:60',
            'banned_alias_at_time' => 'required|string',
            'banner_player_id' => 'required|max:60',
            'reason' => 'required|string',
            'expires_at' => 'nullable|date',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
            'unbanned_at' => 'nullable|date',
            'unbanner_player_id' => 'nullable|max:60',
            'unban_type' => ['nullable', Rule::in(UnbanType::values())],
        ], [
            'in' => 'Invalid :attribute given. Must be ['.UnbanType::allJoined().']',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $ban = GamePlayerBan::create([
            'banned_player_id' => $request->get('banned_player_id'),
            'banned_alias_at_time' => $request->get('banned_alias_at_time'),
            'banner_player_id' => $request->get('banner_player_id'),
            'reason' => $request->get('reason'),
            'expires_at' => $request->get('expires_at'),
            'created_at' => $request->get('created_at'),
            'updated_at' => $request->get('updated_at'),
            'unbanned_at' => $request->get('unbanned_at'),
            'unbanner_player_id' => $request->get('unbanner_player_id'),
            'unban_type' => $request->get('unban_type'),
        ]);

        MinecraftUuidBanned::dispatch($ban);

        return redirect(route('manage.player-bans.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $banId): View
    {
        $ban = GamePlayerBan::find($banId);

        return view('manage.player-bans.edit')
            ->with(compact('ban'));
    }

    public function update(Request $request, int $banId): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'banned_player_id' => 'required|max:60',
            'banned_alias_at_time' => 'required|string',
            'banner_player_id' => 'required|max:60',
            'reason' => 'required|string',
            'expires_at' => 'nullable|date',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
            'unbanned_at' => 'nullable|date',
            'unbanner_player_id' => 'nullable|max:60',
            'unban_type' => ['nullable', Rule::in(UnbanType::values())],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        GamePlayerBan::find($banId)->update($request->all());

        return redirect(route('manage.player-bans.index'));
    }

    public function destroy(Request $request, int $banId): RedirectResponse
    {
        GamePlayerBan::find($banId)->delete();

        return redirect(route('manage.player-bans.index'));
    }
}
