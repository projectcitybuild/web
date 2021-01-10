<?php

namespace App\Http\Controllers;

use App\Entities\Donations\Models\Donation;
use App\Http\WebController;

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

        return view('front.pages.donation-list')->with(compact('donationsThisYear', 'pastDonations'));
    }
}
