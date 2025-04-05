<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\WebController;
use App\Http\Filters\LikeFilter;
use App\Models\GamePlayerBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Pipeline;

final class BanlistController extends WebController
{
    public function index(Request $request)
    {
        $query = $request->input('query');

        $pipes = [
            new LikeFilter('alias', $query, relationship: 'bannedPlayer'),
        ];
        $bans = Pipeline::send(GamePlayerBan::query())
            ->through($pipes)
            ->thenReturn()
            ->with(['bannedPlayer', 'bannerPlayer'])
            ->latest()
            ->paginate(50);

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
