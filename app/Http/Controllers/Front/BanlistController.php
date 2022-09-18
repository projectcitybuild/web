<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\WebController;
use Entities\Models\Eloquent\GameBan;
use Illuminate\Http\Request;

final class BanlistController extends WebController
{
    public function index(Request $request)
    {
        $bans = GameBan::active()->with(['bannedPlayer', 'staffPlayer', 'staffPlayer.aliases'])->latest();

        if ($request->has('query') && $request->input('query') !== '') {
            $query = $request->input('query');
            $bans = GameBan::search($query)->constrain($bans);
        } else {
            $query = '';
        }

        $bans = $bans->paginate(50);

        return view('front.pages.banlist')->with(compact('bans', 'query'));
    }
}
