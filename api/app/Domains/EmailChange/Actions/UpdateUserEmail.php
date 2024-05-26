<?php

namespace App\Domains\EmailChange\Actions;

use App\Models\AccountEmailChange;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

final class UpdateUserEmail
{
    /**
     * @throws ModelNotFoundException if AccountEmailChange for the token is not found
     */
    public function call(string $token)
    {
        $changeRequest = AccountEmailChange::where('token', $token)->firstOrFail();

        DB::transaction(function () use ($changeRequest) {
            $account = $changeRequest->account;
            $account->email = $changeRequest->email_new;
            $account->save();

            $changeRequest->is_confirmed = true;
            $changeRequest->save();
        });
    }
}
