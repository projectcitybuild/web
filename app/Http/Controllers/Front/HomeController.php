<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\WebController;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Money\Money;

final class HomeController extends WebController
{
    public function index()
    {
        Log::info('test');
        $requiredAmount = Money::USD(config('donations.target_funding') * 100);

        $now = now();
        $thisYear = $now->year;
        $lastDayOfThisYear = new Carbon('last day of december');

        $totalDonationsThisYear = Money::USD(Donation::whereYear('created_at', $thisYear)->sum('amount') ?? 0);
        $remainingDaysThisYear = $now->diff($lastDayOfThisYear)->totalDays;

        $lastYear = $now->subYear()->year;
        $totalDonationsLastYear = Money::USD(Donation::whereYear('created_at', $lastYear)->sum('amount') ?? 0);

        return view('front.pages.home.index', [
            'donations' => [
                'raised_last_year' => $totalDonationsLastYear ?: 0,
                'remaining_days' => floor($remainingDaysThisYear),
                'still_required' => $requiredAmount->subtract($totalDonationsThisYear)->getAmount(),
            ],
        ]);
    }
}
