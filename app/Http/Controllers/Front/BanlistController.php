<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\WebController;
use App\Models\GamePlayerBan;
use Illuminate\Http\Request;

final class BanlistController extends WebController
{
    public function index(Request $request)
    {
        $bans = GamePlayerBan::active()
            ->with(['bannedPlayer', 'bannerPlayer'])
            ->latest();

        if ($request->has('query') && !empty($request->input('query'))) {
            $query = $request->input('query');
            $bans = GamePlayerBan::search($query)->constrain($bans);
        } else {
            $query = null;
        }

        $bans = $bans->paginate(50);

        return view('front.pages.banlist')
            ->with([
                'bans' => $bans,
                'query' => $query,
            ]);
    }

    public function show(Request $request, GamePlayerBan $ban)
    {
        return view('front.pages.ban')
            ->with(['ban' => $ban]);
    }
}
