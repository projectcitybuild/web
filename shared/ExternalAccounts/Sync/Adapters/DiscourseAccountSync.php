<?php

namespace Shared\ExternalAccounts\Sync\Adapters;

use Entities\Models\Eloquent\Account;
use GuzzleHttp\Exception\ServerException;
use Library\Discourse\Api\DiscourseAdminApi;
use Library\Discourse\Entities\DiscoursePayload;
use Library\Discourse\Exceptions\UserNotFound;
use Library\Random\RandomStringGenerator;
use Shared\ExternalAccounts\Sync\ExternalAccountSync;

final class DiscourseAccountSync implements ExternalAccountSync
{
    public function __construct(
        private DiscourseAdminApi $discourseAdminApi,
        private RandomStringGenerator $randomStringGenerator,
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

    public function matchExternalUsername(Account $account): void
    {
        try {
            $discourseUser = $this->discourseAdminApi->fetchUserByEmail($account->email);
            $account->username = $discourseUser['username'];
        } catch (UserNotFound) {
            $account->username = $this->randomStringGenerator->generate(length: 10);
        } finally {
            $account->save();
        }
    }
}
