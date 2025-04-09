<?php

namespace App\Http\Controllers\Front\BanAppeal;

use App\Http\Controllers\WebController;
use App\Http\Filters\LikeFilter;
use App\Models\GamePlayerBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Pipeline;

class BanAppealSearchController extends WebController
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user !== null) {
            $activeBans = $user->gamePlayerBans()->active()->get();
        }

        $query = $request->input('query');
        $pipes = [
            new LikeFilter('alias', $query, relationship: 'bannedPlayer'),
        ];
        $bans = Pipeline::send(GamePlayerBan::query())
            ->through($pipes)
            ->thenReturn()
            ->with(['bannedPlayer', 'bannerPlayer'])
            ->active()
            ->latest()
            ->paginate(25);

        return view('front.pages.ban-appeal.search', [
            'active_bans' => $activeBans ?? collect(),
            'bans' => $bans,
            'query' => $query,
        ]);
    }
}
