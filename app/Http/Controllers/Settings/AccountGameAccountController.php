<?php


namespace App\Http\Controllers\Settings;


use App\Http\WebController;
use Illuminate\Http\Request;

class AccountGameAccountController extends WebController
{
    public function index(Request $request)
    {
        $mcAccounts = $request->user()->minecraftAccount;

        return view('front.pages.account.account-game-accounts')->with(compact('mcAccounts'));
    }
}
