<?php

namespace App\Http\Controllers;

use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Http\WebController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

final class HomeController extends WebController
{
    public function index()
    {
        $thisYear = date('Y');
        $now = Carbon::now();
        $lastDayOfThisYear = new Carbon('last day of december');

        $totalDonationsThisYear = Donation::whereYear('created_at', $thisYear)->sum('amount');
        $percentage = round($totalDonationsThisYear / 1000 * 100);
        $remainingDaysThisYear = $lastDayOfThisYear->diff($now)->days;

        // Combine unique Minecraft player count + forum accounts with
        // no game account linked (due to SMF import)
        $playerCount = Cache::remember('front.player_count', 10, function () {
            $minecraftPlayers = MinecraftPlayer::count();
            $unlinkedAccounts = Account::whereDoesntHave('minecraftAccount')->count();

            return $minecraftPlayers + $unlinkedAccounts;
        });

        return view('front.pages.home', [
            'playerCount' => $playerCount,
            'donations' => [
                'total' => $totalDonationsThisYear ? $totalDonationsThisYear : 0,
                'remainingDays' => $remainingDaysThisYear,
                'percentage' => max(1, $percentage) ?: 0,
            ],
        ]);
    }
}
