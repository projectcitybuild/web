<?php

namespace App\Http\Controllers;

use App\Entities\Donations\Models\Donation;
use App\Entities\Servers\Models\Server;
use App\Http\WebController;
use Carbon\Carbon;

final class HomeController extends WebController
{
    public function index()
    {
        $requiredAmount = config('donations.target_funding');

        $now = now();
        $thisYear = $now->year;
        $lastDayOfThisYear = new Carbon('last day of december');

        $totalDonationsThisYear = Donation::whereYear('created_at', $thisYear)->sum('amount');
        $remainingDaysThisYear = $lastDayOfThisYear->diff($now)->days;

        $lastYear = $now->subYear()->year;
        $totalDonationsLastYear = Donation::whereYear('created_at', $lastYear)->sum('amount');

        return view('v2.front.pages.home', [
            'servers' => Server::where('is_visible', true)->with('status')->get(),
            'donations' => [
                'raised_last_year' => $totalDonationsLastYear ?: 0,
                'remaining_days' => $remainingDaysThisYear,
                'still_required' => $requiredAmount - $totalDonationsThisYear,
            ],
        ]);
    }
}
