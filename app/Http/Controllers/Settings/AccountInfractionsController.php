<?php

namespace App\Http\Controllers\Settings;

use App\Http\WebController;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class AccountInfractionsController extends WebController
{
    public function index(Request $request): View
    {
        $account = $request->user();
        $account->load(['warnings', 'gameBans']);

        return view('front.pages.account.account-infractions')
            ->with(compact('account'));
    }
}
