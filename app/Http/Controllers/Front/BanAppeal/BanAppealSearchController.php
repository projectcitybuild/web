<?php

namespace App\Http\Controllers\Front\BanAppeal;

use App\Http\Controllers\WebController;
use App\Models\GamePlayerBan;
use Illuminate\Http\Request;

class BanAppealSearchController extends WebController
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user !== null) {
            $bans = $user->gamePlayerBans()->active()->get();
        }
        return view('front.pages.ban-appeal.search', [
            'active_bans' => $bans ?? collect(),
        ]);
    }
}
