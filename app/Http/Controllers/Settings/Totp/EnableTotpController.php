<?php

namespace App\Http\Controllers\Settings\Totp;

use App\Http\WebController;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class EnableTotpController extends WebController
{
    /**
     * @var Google2FA
     */
    private $google2FA;

    /**
     * EnableTotpController constructor.
     * @param Google2FA $google2FA
     */
    public function __construct(Google2FA $google2FA)
    {
        $this->google2FA = $google2FA;
    }


    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $secret = $this->google2FA->generateSecretKey();
        $request->user()->totp_secret = $secret;
        $request->user()->save();

        r
    }
}
