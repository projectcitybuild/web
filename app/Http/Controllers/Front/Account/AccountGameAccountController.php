<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;

class AccountGameAccountController extends WebController
{
    public function index(Request $request)
    {
        $mcAccounts = $request->user()->minecraftAccount;

        return view('front.pages.account.account-game-accounts')->with(compact('mcAccounts'));
    }

    public function destroy(MinecraftPlayer $minecraftPlayer, Request $request)
    {
        if ($minecraftPlayer->account->id !== $request->user()->id) {
            abort(403, 'You cannot unlink an account that belongs to a different user');
        }

        $minecraftPlayer->account_id = null;
        $minecraftPlayer->save();

        return redirect()->back();
    }
}
