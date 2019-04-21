<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use App\Services\Donations\DonationStatsService;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Accounts\Models\Account;
use Illuminate\Support\Facades\Cache;
use App\Http\WebController;

class HomeController extends WebController
{

    /**
     * @var DonationStatsService
     */
    private $donationStatsService;

    
    public function __construct(DonationStatsService $donationStatsService)
    {
        $this->donationStatsService = $donationStatsService;
    }

    public function getView()
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
