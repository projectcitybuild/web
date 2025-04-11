<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

final class AccountRecordsController extends WebController
{
    public function index(Request $request)
    {
        $user = $request->user();

        $banAppeals = $user->banAppeals;
        $builderRankApplications = $user->builderRankApplications;

        return view(
            'front.pages.account.account-records',
            compact('banAppeals', 'builderRankApplications'),
        );
    }
}
