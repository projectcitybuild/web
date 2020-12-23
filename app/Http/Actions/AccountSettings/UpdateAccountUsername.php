<?php

namespace App\Http\Actions\AccountSettings;

use App\Entities\Accounts\Models\Account;
use App\Library\Discourse\Api\DiscourseAdminApi;
use App\Library\Discourse\Entities\DiscoursePayload;

final class UpdateAccountUsername
{
    private $discourseAdminApi;

    public function __construct(DiscourseAdminApi $discourseAdminApi)
    {
        $this->discourseAdminApi = $discourseAdminApi;
    }

    public function execute(Account $account, string $newUsername): void
    {
        if (empty($newUsername)) {
            throw new \Exception('New email address cannot be empty');
        }

        // Push the email change to Discourse via the user sync route
        $payload = (new DiscoursePayload())
            ->setPcbId($account->getKey())
            ->setEmail($account->email)
            ->setGroups($account->discourseGroupString())
            ->setUsername($newUsername);

        try {
            $this->discourseAdminApi->requestSSOSync($payload->build());
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Sometimes the API fails because the 'requires_activation' key is needed at random in
            // the payload. As a workaround we'll send the request again but with the key included
            // this time
            $payload->requiresActivation(false);
            $this->discourseAdminApi->requestSSOSync($payload->build());
        }

        $account->username = $newUsername;
        $account->save();
    }
}
