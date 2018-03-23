<?php

namespace App\Routes\Http\Web\Controllers;

use Illuminate\Support\Facades\View;
use App\Modules\Donations\Services\DonationStatsService;
use App\Routes\Http\Web\WebController;
use App\Modules\Players\Models\MinecraftPlayer;
use App\Modules\Accounts\Models\Account;

class HomeController extends WebController {
    /**
     * @var DonationStatsService
     */
    private $donationStatsService;

    
    public function __construct(DonationStatsService $donationStatsService) {
        $this->donationStatsService = $donationStatsService;
    }

    public function getView() {
        $donations = $this->donationStatsService->getAnnualPercentageStats();

        // combine unique Minecraft player count + forum accounts with
        // no game account linked (due to SMF import)
        $minecraftPlayers = MinecraftPlayer::count();
        $unlinkedAccounts = Account::whereDoesntHave('minecraftAccount')->count();
        
        $playerCount = $minecraftPlayers + $unlinkedAccounts;

        return view('home', [
            'donations'     => $donations,
            'playerCount'   => $playerCount,
        ]);
    }
}
