<?php

namespace App\Http\Controllers;

use App\Entities\Donations\Models\Donation;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Accounts\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Http\WebController;

final class HomeController extends WebController
{
    public function index()
    {
        $donations = $this->getAnnualPercentageStats();

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

    private function getAnnualPercentageStats() : array
    {
        $year = date('Y');
        $annualSum = Donation::whereYear('created_at', $year)->sum('amount');
        $percentage = round(($annualSum / 1000) * 100);

        $lastDayOfYear = new Carbon('last day of december');
        $now = Carbon::now();
        $remainingDays = $lastDayOfYear->diff($now)->days;

        return [
            'total'         => $annualSum ?: 0,
            'remainingDays' => $remainingDays,
            'percentage'    => max(1, $percentage) ?: 0,
        ];
    }
}
