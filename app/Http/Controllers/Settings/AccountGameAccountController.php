<?php

namespace App\Http\Controllers\Settings;

use App\Entities\Players\Models\MinecraftPlayer;
use App\Http\WebController;
use Illuminate\Http\Request;

class AccountGameAccountController extends WebController
{
    public function index(Request $request)
    {
        $mcAccounts = $request->user()->minecraftAccount;

        return view('v2.front.pages.account.account-game-accounts')->with(compact('mcAccounts'));
    }

    public function destroy(MinecraftPlayer $minecraftPlayer, Request $request)
    {
        if ($minecraftPlayer->account->getKey() !== $request->user()->getKey()) {
            abort(403, 'You cannot unlink an account that belongs to a different user');
        }

        $minecraftPlayer->account_id = null;
        $minecraftPlayer->save();

        return redirect()->back();
    }
}
