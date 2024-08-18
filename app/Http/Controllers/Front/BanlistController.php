<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\WebController;
use App\Models\GamePlayerBan;
use Illuminate\Http\Request;

final class BanlistController extends WebController
{
    public function index(Request $request)
    {
        $bans = GamePlayerBan::active()->with(['bannedPlayer', 'bannerPlayer', 'bannerPlayer.aliases'])->latest();

        if ($request->has('query') && $request->input('query') !== '') {
            $query = $request->input('query');
            $bans = GamePlayerBan::search($query)->constrain($bans);
        } else {
            $query = '';
        }

        $bans = $bans->paginate(50);

        return view('front.pages.banlist')->with(compact('bans', 'query'));
    }
}
