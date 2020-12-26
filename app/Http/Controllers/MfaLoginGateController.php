<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class MfaLoginGateController extends WebController
{
    public function create()
    {
        if (!Session::has('auth.needs-2fa')) {
            abort(403);
        }

        return view('front.pages.login.mfa');
    }

    public function store(Request $request)
    {
        if (Session::has('auth.needs-mfa')) {
            abort(403);
        }

        $keyTimestamp = $this->google2FA->verifyKeyNewer(
            Crypt::decryptString($request->user()->totp_secret),
            $request->code,
            $request->user()->totp_last_used
        );

        if ($keyTimestamp === false) {
            return back()->withErrors([
                'code' => 'This 2FA code isn\'t valid. Please try again.'
            ]);
        }

        $request->user()->totp_last_used = $keyTimestamp;
        $request->user()->save();

        Session::forget('auth.needs-mfa');

        return redirect()->intended(route('front.sso.discourse'));
    }
}
