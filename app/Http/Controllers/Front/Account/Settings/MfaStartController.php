<?php

namespace App\Http\Controllers\Front\Account\Settings;

use App\Http\Controllers\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class MfaStartController extends WebController
{
    public function __construct(
        private readonly Google2FA $google2FA,
    ) {}

    public function __invoke(Request $request)
    {
        if ($request->user()->is_totp_enabled) {
            abort(403);
        }

        $secret = $this->google2FA->generateSecretKey();
        $backupCode = Str::random(config('auth.totp.backup_code_length'));
        $request->user()->totp_secret = Crypt::encryptString($secret);
        $request->user()->totp_backup_code = Crypt::encryptString($backupCode);
        $request->user()->save();

        return redirect()->route('front.account.settings.mfa.setup');
    }
}
