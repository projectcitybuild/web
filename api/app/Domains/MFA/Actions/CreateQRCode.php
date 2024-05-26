<?php

namespace App\Domains\MFA\Actions;

use App\Models\Account;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\TwoFactorAuthException;

class CreateQRCode
{
    public function __construct(
        private readonly TwoFactorAuth $auth,
    ) {}

    /**
     * @throws TwoFactorAuthException if QR code generation fails
     */
    public function call(Account $account): string
    {
        if ($account->two_factor_secret === null) {
            // TODO: throw a regular exception instead of calling a HTTP abort
            abort(code: 404, message: 'Two Factor Authentication must be enabled first');
        }
        return $this->auth->getQRCodeImageAsDataUri(
            label: $account->username,
            secret: decrypt($account->two_factor_secret),
        );
    }
}
