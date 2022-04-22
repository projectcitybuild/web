<?php

namespace Domain\EmailChange\UseCases;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\AccountEmailChange;
use Illuminate\Support\Facades\DB;
use Shared\ExternalAccounts\Sync\ExternalAccountSync;

final class UpdateAccountEmailUseCase
{
    public function __construct(
        private ExternalAccountSync $externalAccountSync,
    ) {}

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

            $this->externalAccountSync->sync(account: $account);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
