<?php

namespace App\Http\Controllers;

use App\Http\Middleware\MfaGate;
use App\Http\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Google2FA;

class MfaLoginGateController extends WebController
{
    private Google2FA $google2FA;

    /**
     * MfaLoginGateController constructor.
     */
    public function __construct(Google2FA $google2FA)
    {
        $this->google2FA = $google2FA;
    }

    public function create()
    {
        return view('v2.front.pages.login.mfa');
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

        return redirect()->intended(route('front.sso.discourse'));
    }
}
