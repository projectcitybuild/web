<?php

namespace App\Http\Actions\AccountPasswordReset;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\AccountPasswordReset;
use App\Entities\Notifications\AccountPasswordResetNotification;
use App\Helpers\TokenHelpers;
use Illuminate\Support\Carbon;

final class SendPasswordResetEmail
{
    public function execute(Account $account, string $email)
    {
        $passwordReset = AccountPasswordReset::updateOrCreate([
            'email' => $email,
        ], [
            'token' => TokenHelpers::generateToken(),
            'created_at' => Carbon::now(),
        ]);

        $account->notify(new AccountPasswordResetNotification($passwordReset));
    }
}
