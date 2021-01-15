<?php

namespace App\Http\Controllers;

use App\Entities\Donations\Models\Donation;
use App\Http\WebController;
use DB;

final class DonationListController extends WebController
{
    public function index()
    {
        $donationsThisYear = Donation::with('account.minecraftAccount.aliases')
            ->whereYear('created_at', '=', now()->year)
            ->orderBy('created_at', 'desc')
            ->get();

        $pastDonations = Donation::with('account.minecraftAccount.aliases')
            ->whereYear('created_at', '<', now()->year)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $highestNonAnonymousDonators = Donation::with('account.minecraftAccount.aliases')
            ->select(DB::raw('SUM(amount) AS total_amount'), 'account_id')
            ->groupBy('account_id')
            ->orderBy('total_amount', 'desc')
            ->whereHas('account')
            ->take(10)
            ->get();

        $highestAnonymousDonations = Donation::whereDoesntHave('account')
            ->orderBy('amount', 'desc')
            ->take(10)
            ->get()
            ->map(function ($donation) {
                $donation->total_amount = $donation->amount;

                return $donation;
            });

        $highestDonators = $highestNonAnonymousDonators;

        foreach ($highestAnonymousDonations as $donation) {
            $highestDonators->push($donation);
        }

        $highestDonators = $highestDonators
            ->sortByDesc(function ($donation, $_) {
                return $donation->total_amount;
            })
            ->take(10);

        return view('front.pages.donation-list')
            ->with(compact(
                'donationsThisYear',
                'pastDonations',
                'highestDonators'
            ));
    }
}
