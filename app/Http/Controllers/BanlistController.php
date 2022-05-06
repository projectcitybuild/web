<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use Entities\Models\Eloquent\GameBan;
use Illuminate\Http\Request;

final class BanlistController extends WebController
{
    public function index(Request $request)
    {
        $bans = GameBan::where('is_active', 1)->with(['bannedPlayer', 'staffPlayer', 'staffPlayer.aliases'])->latest();

        if ($request->has('query') && $request->input('query') !== '') {
            $query = $request->input('query');
            $bans = GameBan::search($query)->constrain($bans);
        } else {
            $query = '';
        }

        $bans = $bans->paginate(5);

        return view('v2.front.pages.banlist')->with(compact('bans', 'query'));
    }
}
