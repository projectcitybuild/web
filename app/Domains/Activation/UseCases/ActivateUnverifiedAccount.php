<?php

namespace App\Domains\Activation\UseCases;

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

        $activation = AccountActivation::where('token', $token)->firstOrFail();

        DB::transaction(function () use ($account, $activation) {
            $account->activated = true;
            $account->save();

            $activation->delete();
        });
    }
}
