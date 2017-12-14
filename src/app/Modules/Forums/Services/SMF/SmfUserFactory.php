<?php
namespace App\Modules\Forums\Services\SMF;

use App\Modules\Forums\Repositories\ForumUserRepository;

class SmfUserFactory {
    
    /**
     * Repository for ForumUser models
     *
     * @var repository
     */
    private $repository;

    /**
     * List of all group ids considered to be 'staff'
     *
     * @var array
     */
    private $staffGroupIds;


    public function __construct(ForumUserRepository $repository, array $staffGroupIds) {
        $this->repository = $repository;
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
            $this->repository,
            $this->staffGroupIds
        );
    }
}