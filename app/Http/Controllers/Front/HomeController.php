<?php

namespace App\Http\Controllers\Front;

use App\Domains\Instagram\InstagramAuth;
use App\Domains\Instagram\InstagramMediaFeed;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerSession;
use Illuminate\View\View;

class HomeController
{
    public function __invoke(InstagramMediaFeed $instagramMediaFeed, InstagramAuth $instagramAuth): View
    {
        $recentPlayers = MinecraftPlayer::query()
            ->whereHas('sessions')
            ->with(['latestSession', 'account.roles'])
            ->orderByDesc(
                MinecraftPlayerSession::query()
                    ->select('starts_at')
                    ->whereColumn('player_id', 'players.id')
                    ->orderByDesc('starts_at')
                    ->orderByDesc('id')
                    ->limit(1),
            )
            ->orderByDesc('players.id')
            ->limit(16)
            ->get();

        $instagramFeedConfigured = $instagramAuth->isConfigured();
        $instagramImages = $instagramFeedConfigured
            ? $instagramMediaFeed->latestImages()
            : null;

        return view('front.pages.home.index', [
            'recentPlayers' => $recentPlayers,
            'instagramFeedConfigured' => $instagramFeedConfigured,
            'instagramImages' => $instagramImages,
        ]);
    }
}
