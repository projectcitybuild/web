<?php

namespace App\Http\Controllers\Front\Auth\Mfa;

use App\Http\Controllers\WebController;
use App\Http\Middleware\MfaGate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Google2FA;

class MfaLoginGateController extends WebController
{
    public function __construct(
        private Google2FA $google2FA
    ) {
    }

    public function show()
    {
        return view('front.pages.auth.mfa.mfa');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);

        $keyTimestamp = $this->google2FA->verifyKeyNewer(
            Crypt::decryptString($request->user()->totp_secret),
            $request->code,
            $request->user()->totp_last_used
        );

        if ($keyTimestamp === false) {
            return back()->withErrors([
                'code' => 'This 2FA code isn\'t valid. Please try again.',
            ]);
        }

        $request->user()->totp_last_used = $keyTimestamp;
        $request->user()->save();

        Session::forget(MfaGate::NEEDS_MFA_KEY);

        return redirect()->intended();
    }
}
