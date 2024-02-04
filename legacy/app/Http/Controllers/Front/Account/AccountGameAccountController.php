<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\WebController;
use Entities\Models\Eloquent\MinecraftPlayer;
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
        if ($minecraftPlayer->account->getKey() !== $request->user()->getKey()) {
            abort(403, 'You cannot unlink an account that belongs to a different user');
        }

        $minecraftPlayer->account_id = null;
        $minecraftPlayer->disableLogging()->save();
        activity()
            ->on($minecraftPlayer)
            ->withProperty('old', ['account_id' => $request->user()->getKey()])
            ->withProperty('attributes', ['account_id' => null])
            ->event('updated')
            ->log('unlinked from account');

        return redirect()->back();
    }
}
