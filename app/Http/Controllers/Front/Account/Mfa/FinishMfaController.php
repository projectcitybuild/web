<?php

namespace App\Http\Controllers\Front\Account\Mfa;

use App\Core\Domains\Google2FA\Notifications\AccountMfaEnabledNotification;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class FinishMfaController extends WebController
{
    /**
     * @var Google2FA
     */
    private $google2FA;

    /**
     * EnableTotpController constructor.
     */
    public function __construct(Google2FA $google2FA)
    {
        $this->google2FA = $google2FA;
    }

    /**
     * Handle the incoming request.
     *
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws ValidationException
     */
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

        $request->user()->notify(new AccountMfaEnabledNotification());

        return redirect()->route('front.account.security')->with([
            'mfa_setup_finished' => true,
        ]);
    }
}
