<?php


namespace App\Http\Actions;


use App\Entities\Accounts\Models\Account;
use App\Library\Discourse\Entities\DiscoursePayload;

class SyncUserToDiscourse
{
    /**
     * @var Account
     */
    private $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function syncAll()
    {
        $payload = (new DiscoursePayload)
            ->setPcbId($this->account->getKey())
            ->setEmail($this->account->email)
            ->setUsername($this->account->username)
            ->setGroups($this->account->discourseGroupString());

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
