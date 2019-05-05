<?php

namespace App\Http\Controllers;

use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Accounts\Models\Account;
use Illuminate\Support\Facades\Cache;
use App\Http\WebController;
use App\Entities\Donations\Repositories\DonationRepository;
use Carbon\Carbon;

final class HomeController extends WebController
{
    private $donationRepository;

    public function __construct(DonationRepository $donationRepository)
    {
        $this->donationRepository = $donationRepository;
    }

    public function getView()
    {
        $annualSum = $this->donationRepository->getAnnualSum();
        $percentage = round(($annualSum / 1000) * 100);

        $lastDayOfYear = new Carbon('last day of december');
        $now = Carbon::now();
        $remainingDays = $lastDayOfYear->diff($now)->days;


        // combine unique Minecraft player count + forum accounts with
        // no game account linked (due to SMF import)
        $playerCount = Cache::remember('front.player_count', 10, function () {
            $minecraftPlayers = MinecraftPlayer::count();
            $unlinkedAccounts = Account::whereDoesntHave('minecraftAccount')->count();
            
            return $minecraftPlayers + $unlinkedAccounts;
        });

        return view('front.pages.home', [
            'playerCount' => $playerCount,
            'donations' => [
                'total'         => $annualSum ?: 0,
                'remainingDays' => $remainingDays,
                'percentage'    => max(1, $percentage) ?: 0,
            ],
        ]);
    }
}
