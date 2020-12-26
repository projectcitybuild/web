<?php

namespace App\Http\Controllers\Settings\Mfa;

use App\Http\WebController;
use Illuminate\Http\Request;

class DisableMfaController extends WebController
{
    public function show(Request $request)
    {
        if (!$request->user()->is_totp_enabled) {
            abort(403);
        }

        return view('front.pages.account.security.2fa-disable');
    }

    public function destroy(Request $request)
    {
        $account = $request->user();
        $account->resetTotp();
        $account->save();

        return redirect(route('front.account.security'))->with(['mfa_disabled' => true]);
    }
}
