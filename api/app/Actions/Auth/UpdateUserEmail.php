<?php

namespace App\Actions\Auth;

use App\Models\Eloquent\Account;
use App\Models\Eloquent\AccountEmailChange;
use App\Notifications\VerifyNewEmailNotification;
use App\Services\HashedTokenGenerator;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

final class UpdateUserEmail
{
    /**
     * @throws Exception if AccountEmailChange for the token is not found
     */
    public function update(string $token)
    {
        $changeRequest = AccountEmailChange::where('token', $token)->first();

        if ($changeRequest === null) {
            throw new Exception("Invalid token");
        }

        DB::transaction(function () use ($changeRequest) {
            $account = $changeRequest->account;
            $account->email = $changeRequest->email_new;
            $account->save();

            $changeRequest->is_confirmed = true;
            $changeRequest->save();
        });
    }
}
