<?php
/**
 * Provides methods to interact with SMF outside of the forums
 *
 * @package App\Modules\Forums\Services\SMF
 */
namespace App\Modules\Forums\Services\SMF;

use App\Modules\Forums\Models\{ForumPost, ForumTopic, ForumBoard, ForumUser};
use App\Modules\Forums\Repositories\ForumUserRepository;
use Carbon\Carbon;
use DB;

class Smf {

    /**
     * The name of the SMF cookie that we intercept to retrieve
     * data about the logged-in forum user
     *
     * @var string
     */
    private $cookieName;

    /**
     * @var SmfUserFactory
     */
    private $userFactory;

    /**
     * SmfUser instance
     *
     * @var SmfUser
     */
    private $user;

    /**
     * Repository for ForumUser models
     *
     * @var ForumUserRepository
     */
    private $repository;


    public function __construct(ForumUserRepository $repository, string $cookieName, SmfUserFactory $userFactory) {
        $this->cookieName = $cookieName;
        $this->userFactory = $userFactory;
        $this->repository = $repository;
    }

    /**
     * Returns the cookie used by SMF
     *
     * @return void
     */
    protected function getSmfCookie() {
        return @$_COOKIE[$this->cookieName];
    }

    /**
     * Gets an instance of SmfUser using cookie data
     *
     * @return SmfUser
     */
    public function getUser() : SmfUser {
        if(isset($this->user)) {
            return $this->user;
        }

        $cookie = $this->getSmfCookie();
        if(isset($cookie)) {
            $cookieData = unserialize($cookie);

            // authenticate to prevent cookie forgery
            $forumUser = $this->repository->getUserById($cookieData[0]);
            if(isset($forumUser)) {
                // SMF uses sha1 so we need to match that...
                $storedPass = sha1($forumUser->passwd . $forumUser->password_salt);
                if($cookieData[1] === $storedPass) {
                    $this->user = $this->userFactory->getInstance($cookieData[0]);
                    return $this->user;
                }
            }
        } 
        
        $this->user = $this->userFactory->getInstance(-1);
        return $this->user;
    }

}