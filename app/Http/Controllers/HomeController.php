<?php

namespace App\Http\Controllers;

use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Servers\Models\Server;
use App\Http\WebController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

final class HomeController extends WebController
{
    public function index()
    {
        return view('v2.front.pages.home', [
            'donations' => $this->getDonationData(),
            'servers' => Server::where('is_visible', true)->with('status')->get(),
        ]);
    }

    private function getDonationData(): array
    {
        $requiredAmount = 1000;

        $now = Carbon::now();
        $thisYear = $now->year;
        $lastDayOfThisYear = new Carbon('last day of december');

        $totalDonationsThisYear = Donation::whereYear('created_at', $thisYear)->sum('amount');
        $percentage = round($totalDonationsThisYear / $requiredAmount * 100);
        $remainingDaysThisYear = $lastDayOfThisYear->diff($now)->days;

        $lastYear = $now->subYear()->year;
        $totalDonationsLastYear = Donation::whereYear('created_at', $lastYear)->sum('amount');

        return [
            'raised_this_year' => $totalDonationsThisYear ?: 0,
            'raised_last_year' => $totalDonationsLastYear ?: 0,
            'remaining_days' => $remainingDaysThisYear,
            'percentage' => max(1, $percentage) ?: 0,
            'still_required' => $requiredAmount - $totalDonationsThisYear,
        ];
    }
}
