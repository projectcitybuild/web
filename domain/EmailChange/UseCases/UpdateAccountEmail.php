<?php

namespace Domain\EmailChange\UseCases;

use App\Models\Account;
use App\Models\AccountEmailChange;
use Illuminate\Support\Facades\DB;

final class UpdateAccountEmail
{
    public function execute(Account $account, AccountEmailChange $emailChangeRequest)
    {
        $newEmailAddress = $emailChangeRequest->email_new;

        if (empty($newEmailAddress)) {
            throw new \Exception('New email address cannot be empty');
        }

        DB::beginTransaction();
        try {
            $account->email = $newEmailAddress;
            $account->save();

            $emailChangeRequest->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
