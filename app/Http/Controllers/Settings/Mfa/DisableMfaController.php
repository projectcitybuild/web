<?php

namespace App\Http\Controllers\Settings\Mfa;

use App\Entities\Accounts\Notifications\AccountMfaDisabledNotification;
use App\Http\WebController;
use Illuminate\Http\Request;

class DisableMfaController extends WebController
{
    public function show(Request $request)
    {
        if (! $request->user()->is_totp_enabled) {
            return redirect(route('front.account.security'));
        }

        return view('v2.front.pages.account.security.2fa-disable');
    }

    public function destroy(Request $request)
    {
        $account = $request->user();
        $account->resetTotp();
        $account->save();
        $account->notify(new AccountMfaDisabledNotification());

        return redirect(route('front.account.security'))->with(['mfa_disabled' => true]);
    }
}
