<?php

namespace App\Domains\MFA\Actions;

use App\Models\Account;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\TwoFactorAuthException;

class EnableMFA
{
    public function __construct(
        private readonly TwoFactorAuth $auth,
    ) {}

    /**
     * @throws TwoFactorAuthException if MFA secret generation fails
     */
    public function call(Account $account): void
    {
        if ($account->two_factor_secret !== null) {
            // TODO: throw a regular exception instead of calling a HTTP abort
            abort(code: 409, message: 'Two Factor Authentication is already enabled');
        }
        $account->two_factor_secret = encrypt($this->auth->createSecret());
        $account->save();
    }
}
