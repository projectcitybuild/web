<?php

namespace App\Http\Actions\AccountSettings;

use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Models\AccountEmailChange;
use App\Library\Discourse\Api\DiscourseAdminApi;
use App\Library\Discourse\Entities\DiscoursePayload;
use Illuminate\Support\Facades\DB;

final class UpdateAccountEmail
{
    private $discourseAdminApi;

    public function __construct(DiscourseAdminApi $discourseAdminApi)
    {
        $this->discourseAdminApi = $discourseAdminApi;
    }

    public function execute(Account $account, AccountEmailChange $emailChangeRequest)
    {
        $newEmailAddress = $emailChangeRequest->email_new;

        if (empty($newEmailAddress)) {
            throw new \Exception('New email address cannot be empty');
        }

        DB::beginTransaction();
        try {
            // Notify Discourse that the user's email address has changed
            $this->updateDiscourseAccount($account->getKey(), $newEmailAddress);

            $account->email = $newEmailAddress;
            $account->save();

            $emailChangeRequest->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function updateDiscourseAccount(int $pcbAccountId, string $newEmailAddress)
    {
        $payload = (new DiscoursePayload())
            ->setPcbId($pcbAccountId)
            ->setEmail($newEmailAddress);

        try {
            $this->discourseAdminApi->requestSSOSync($payload->build());
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Sometimes the API fails because the 'requires_activation' key is needed at random in
            // the payload. As a workaround we'll send the request again but with the key included
            // this time
            $payload->requiresActivation(false);
            $this->discourseAdminApi->requestSSOSync($payload->build());
        }
    }
}
