<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Accounts\Models\Account;
use App\Http\WebController;
use App\Library\Discourse\Api\DiscourseUserApi;

class AccountDiscourseAdminRedirect extends WebController
{
    /**
     * @var DiscourseUserApi
     */
    private $discourseUserApi;

    /**
     * AccountDiscourseAdminRedirect constructor.
     */
    public function __construct(DiscourseUserApi $discourseUserApi)
    {
        $this->discourseUserApi = $discourseUserApi;
    }

    public function __invoke(Account $account)
    {
        $discourseUser = $this->discourseUserApi->fetchUserByPcbId($account->account_id);
        $discourseId = $discourseUser['user']['id'];
        $discourseUsername = $discourseUser['user']['username'];

        return redirect("https://forums.projectcitybuild.com/admin/users/${discourseId}/${discourseUsername}");
    }
}
