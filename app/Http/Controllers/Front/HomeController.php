<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\WebController;
use App\Models\Donation;
use App\Models\Server;
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

        return view('front.pages.home.index', [
            'servers' => Server::get(),
            'donations' => [
                'raised_last_year' => $totalDonationsLastYear ?: 0,
                'remaining_days' => $remainingDaysThisYear,
                'still_required' => $requiredAmount - $totalDonationsThisYear,
            ],
        ]);
    }
}
