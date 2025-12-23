<?php

namespace App\Http\Controllers\Front\Account\Settings;

use App\Domains\Mfa\Notifications\MfaEnabledNotification;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class MfaFinishController extends WebController
{
    public function __construct(
        private readonly Google2FA $google2FA,
    ) {}

    public function __invoke(Request $request)
    {
        if ($request->user()->is_totp_enabled) {
            abort(403);
        }

        $request->validate([
            'backup_saved' => 'accepted',
            'code' => 'required',
        ]);

        $keyTimestamp = $this->google2FA->verifyKeyNewer(
            Crypt::decryptString($request->user()->totp_secret),
            $request->code,
            $request->user()->totp_last_used
        );

        // Check validity of code
        if ($keyTimestamp === false) {
            throw ValidationException::withMessages([
                'code' => ['This code isn\'t valid'],
            ]);
        }

        $request->user()->is_totp_enabled = true;
        $request->user()->totp_last_used = $keyTimestamp;
        $request->user()->save();

        $request->user()->notify(new MfaEnabledNotification);

        return redirect()
            ->route('front.account.settings.mfa')
            ->with(['success' => '2FA has successfully been enabled on your account']);
    }
}
