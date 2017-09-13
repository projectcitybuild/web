<?php
namespace App\Modules\Forums\Repositories;

use App\Modules\Forums\Models\ForumUser;

class ForumUserRepository {

    private $model;

    public function __construct(ForumUser $model) {
        $this->model = $model;
    }
    
    /**
     * Returns the ForumUser that has the given id
     *
     * @param int $forumUserId
     * @return ForumUser
     */
    public function getUserById(int $forumUserId) : ?ForumUser {
        return $this->model->find($forumUserId);
    }

}