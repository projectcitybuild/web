<?php

namespace App\Core\Support\Passport;

use Admin9\OidcServer\Authentication\ReauthenticationChallenge;
use Admin9\OidcServer\Contracts\ReauthenticationHandler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class OidcReauthenticationHandler implements ReauthenticationHandler
{
    public function redirect(Request $request, ReauthenticationChallenge $challenge): RedirectResponse
    {
        return redirect()
            ->route('front.oidc.reauth', [
                'transaction' => $challenge->transactionId,
                'challenge' => $challenge->token,
            ])
            ->header('Cache-Control', 'no-store')
            ->header('Referrer-Policy', 'no-referrer');
    }
}
