<?php

namespace App\Http\Controllers\Front\Account\Settings;

use App\Core\Domains\Mfa\Notifications\MfaDisabledNotification;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

class MfaDisableController extends WebController
{
    public function show(Request $request)
    {
        if (! $request->user()->is_totp_enabled) {
            return redirect(route('front.account.security'));
        }

        return view('front.pages.account.settings.mfa-disable');
    }

    public function destroy(Request $request)
    {
        $account = $request->user();
        $account->resetTotp();
        $account->save();
        $account->notify(new MfaDisabledNotification());

        return redirect()
            ->route('front.account.settings.mfa')
            ->with(['success' => '2FA has successfully been removed from your account']);
    }
}
