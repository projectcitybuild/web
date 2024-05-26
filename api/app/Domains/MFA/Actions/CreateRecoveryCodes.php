<?php

namespace App\Domains\MFA\Actions;

use App\Models\Account;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CreateRecoveryCodes
{
    public function call(Account $account): array
    {
        if ($account->two_factor_secret === null) {
            // TODO: throw a regular exception instead of calling a HTTP abort
            abort(code: 404, message: 'Two Factor Authentication must be enabled first');
        }

        $generator = fn() => $this->generateCode();
        $codes = Collection::times(8, $generator)->all();

        $account->two_factor_recovery_codes = encrypt(json_encode($codes));
        $account->save();

        return $codes;
    }

    private function generateCode(): string
    {
        return Str::random(10).'-'.Str::random(10);
    }
}
