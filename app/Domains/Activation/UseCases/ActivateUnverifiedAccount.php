<?php

namespace App\Domains\Activation\UseCases;

use App\Core\Data\Exceptions\ForbiddenException;
use App\Domains\Activation\Exceptions\AccountAlreadyActivatedException;
use App\Models\Account;
use App\Models\AccountActivation;
use Illuminate\Support\Facades\DB;

final class ActivateUnverifiedAccount
{
    public function execute(Account $account, string $token)
    {
        if ($account->activated) {
            throw new AccountAlreadyActivatedException();
        }

        $activation = AccountActivation::where('token', $token)
            ->whereNotExpired()
            ->firstOrFail();

        if ($activation->account_id !== $account->getKey()) {
            throw new ForbiddenException(id: 'account_mismatch', message: 'Account mismatch');
        }

        DB::transaction(function () use ($account, $activation) {
            $account->activated = true;
            $account->save();

            $activation->delete();
        });
    }
}
