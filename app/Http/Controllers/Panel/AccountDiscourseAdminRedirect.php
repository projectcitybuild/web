<?php

namespace App\Http\Controllers\Panel;

use App\Http\WebController;
use Entities\Models\Eloquent\Account;
use GuzzleHttp\Exception\ClientException;
use Library\Discourse\Api\DiscourseUserApi;

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
        try {
            $discourseUser = $this->discourseUserApi->fetchUserByPcbId($account->account_id);
        } catch (ClientException $e) {
            return redirect()->back()->with([
                'message_type' => 'danger',
                'message' => 'User does not have a Discourse profile.',
            ]);
        }

        $discourseId = $discourseUser['user']['id'];
        $discourseUsername = $discourseUser['user']['username'];

        return redirect("https://forums.projectcitybuild.com/admin/users/${discourseId}/${discourseUsername}");
    }
}
