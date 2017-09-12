<?php
/**
 * Provides methods to interact with SMF outside of the forums
 *
 * @package App\Modules\Forums\Services\SMF
 */
namespace App\Modules\Forums\Services\SMF;

use App\Modules\Forums\Models\{ForumPost, ForumTopic, ForumBoard, ForumUser};
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
     * List of all group ids considered to be 'staff'
     *
     * @var array
     */
    private $staffGroupIds;

    /**
     * SmfUser instance
     *
     * @var SmfUser
     */
    private $user;

    /**
     * ForumUser model
     *
     * @var ForumUser
     */
    private $model;


    public function __construct(ForumUser $model, string $cookieName, array $staffGroupIds) {
        $this->cookieName = $cookieName;
        $this->staffGroupIds = $staffGroupIds;
        $this->model = $model;
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

        $cookie = @$_COOKIE[$this->cookieName];
        if(isset($cookie)) {
            $cookieData = unserialize($cookie);

            // authenticate to prevent cookie forgery
            $forumUser = $this->model->find($cookieData[0]);
            if(isset($forumUser)) {
                // SMF uses sha1 so we need to match that...
                $storedPass = sha1($forumUser->passwd . $forumUser->password_salt);
                if($cookieData[1] == $storedPass) {
                    $this->user = new SmfUser($cookieData[0], $this->staffGroupIds);
                }
            }

        } else {
            $this->user = new SmfUser(null, $this->staffGroupIds);
        }

        return $this->user;
    }

}