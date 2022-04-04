<?php

namespace Shared\ExternalAccounts\Adapters;

use App\Entities\Models\Eloquent\Account;
use App\Library\Discourse\Api\DiscourseAdminApi;
use App\Library\Discourse\Entities\DiscoursePayload;
use GuzzleHttp\Exception\ServerException;
use Shared\ExternalAccounts\ExternalAccountSync;

final class DiscourseAccountSync implements ExternalAccountSync
{
    public function __construct(
        private DiscourseAdminApi $discourseAdminApi,
    ) {}

    public function sync(Account $account): void
    {
        $payload = (new DiscoursePayload())
            ->setPcbId($account->getKey())
            ->setEmail($account->email)
            ->setUsername($account->username)
            ->setGroups($account->discourseGroupString());

        try {
            $this->discourseAdminApi->requestSSOSync($payload->build());
        } catch (ServerException) {
            // Sometimes the API fails because the 'requires_activation' key is needed at random in
            // the payload. As a workaround we'll send the request again but with the key included
            // this time
            $payload->requiresActivation(false);
            $this->discourseAdminApi->requestSSOSync($payload->build());
        }
    }
}
