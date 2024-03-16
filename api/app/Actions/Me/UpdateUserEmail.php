<?php

namespace App\Actions\Me;

use App\Models\Eloquent\AccountEmailChange;
use Exception;
use Illuminate\Support\Facades\DB;

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
