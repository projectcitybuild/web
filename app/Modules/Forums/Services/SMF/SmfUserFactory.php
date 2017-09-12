<?php
namespace App\Modules\Forums\Services\SMF;

use App\Modules\Forums\Models\ForumUser;

class SmfUserFactory {
    
    /**
     * Model representation of a SMF user
     *
     * @var ForumUser
     */
    private $forumUserModel;

    /**
     * List of all group ids considered to be 'staff'
     *
     * @var array
     */
    private $staffGroupIds;


    public function __construct(ForumUser $forumUserModel, array $staffGroupIds) {
        $this->forumUserModel = $forumUserModel;
        $this->staffGroupIds = $staffGroupIds;
    }

    /**
     * Returns a new SmfUser instance
     *
     * @param int $forumUserId  SMF user id
     * @return SmfUser
     */
    public function getInstance(int $forumUserId) : SmfUser {
        return new SmfUser(
            $forumUserId, 
            $this->forumUserModel,
            $this->staffGroupIds
        );
    }
}