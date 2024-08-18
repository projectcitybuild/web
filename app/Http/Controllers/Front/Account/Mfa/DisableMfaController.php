<?php

namespace App\Http\Controllers\Front\Account\Mfa;

use App\Core\Domains\Mfa\Notifications\AccountMfaDisabledNotification;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

class DisableMfaController extends WebController
{
    public function show(Request $request)
    {
        if (! $request->user()->is_totp_enabled) {
            return redirect(route('front.account.security'));
        }

        return view('front.pages.account.security.2fa-disable');
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
