<?php

namespace App\Http\Controllers;

use App\Services\Donations\DonationStatsService;
use App\Entities\Eloquent\Players\Models\MinecraftPlayer;
use App\Entities\Eloquent\Accounts\Models\Account;
use Illuminate\Support\Facades\Cache;
use App\Http\WebController;

final class HomeController extends WebController
{
    /**
     * @var DonationStatsService
     */
    private $donationStatsService;

    
    public function __construct(DonationStatsService $donationStatsService)
    {
        $this->donationStatsService = $donationStatsService;
    }

    public function index()
    {
        $donations = $this->donationStatsService->getAnnualPercentageStats();

        // combine unique Minecraft player count + forum accounts with
        // no game account linked (due to SMF import)
        $playerCount = Cache::remember('front.player_count', 10, function () {
            $minecraftPlayers = MinecraftPlayer::count();
            $unlinkedAccounts = Account::whereDoesntHave('minecraftAccount')->count();
            
            return $minecraftPlayers + $unlinkedAccounts;
        });

        return view('front.pages.home', [
            'donations'     => $donations,
            'playerCount'   => $playerCount,
        ]);
    }
}
