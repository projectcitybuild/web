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
            ->setGroups();
    }
}
