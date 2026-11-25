<?php

namespace App\Http\Controllers\Front;

use App\Core\Domains\Instagram\InstagramMediaFeed;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerSession;
use Illuminate\View\View;

class HomeController
{
    public function __invoke(InstagramMediaFeed $instagramMediaFeed): View
    {
        $recentPlayers = MinecraftPlayer::query()
            ->whereHas('sessions')
            ->with('latestSession')
            ->orderByDesc(
                MinecraftPlayerSession::query()
                    ->select('starts_at')
                    ->whereColumn('player_id', 'players.id')
                    ->orderByDesc('starts_at')
                    ->orderByDesc('id')
                    ->limit(1),
            )
            ->orderByDesc('players.id')
            ->limit(12)
            ->get();
        $instagramFeedConfigured = $instagramMediaFeed->isConfigured();
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
