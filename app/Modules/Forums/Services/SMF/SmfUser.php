<?php
/**
 * Represents either a logged-in user or a guest
 *
 * @package App\Modules\Forums\Services\SMF
 */
namespace App\Modules\Forums\Services\SMF;

use App\Modules\Forums\Models\ForumUser;

class SmfUser {

    /**
     * User id of the SMF user. -1 if guest
     *
     * @var int
     */
    private $userId;

    /**
     * List of all group ids considered to be 'staff'
     *
     * @var array
     */
    private $staffGroupIds;

    /**
     * Model representation of a SMF user
     * 
     * @var ForumUser
     */
    private $forumUserModel;

    /**
     * Array of groups the user belongs to
     *
     * @var array
     */
    private $groups;


    public function __construct(int $smfUserId, ForumUser $forumUserModel, array $staffGroups) {
        $this->userId = $smfUserId;
        $this->forumUserModel = $forumUserModel;
        $this->staffGroups = $staffGroups;
    }

    /**
     * Returns whether the user is a guest or not
     *
     * @return bool
     */
    public function isGuest() : bool {
        return $this->smfUserId === -1;
    }

    /**
     * Returns the user's forum account id
     *
     * @return int
     */
    public function getId() : int {
        return $this->isGuest() ? null : $this->userId;
    }

    /**
     * Fetches a ForumUser of the user from storage, if logged in
     *
     * @return ForumUser
     */
    public function getUserFromDatabase() : ?ForumUser {
        if(isset($this->user)) {
            return $this->user;
        }

        $this->user = $this->forumUserModel
            ->where('id_member', $this->userId)
            ->first();

        return $this->user;
    }

    /**
     * Fetches a collecction of ForumMembergroup the user belongs to
     *
     * @return array
     */
    public function getUserGroupsFromDatabase() : array {
        if(isset($this->groups)) {
            return $this->groups;
        }

        $user = $this->getUserfromDatabase();
        if(empty($user)) {
            return [0];
        }

        // combine primary and secondary groups
        $groups = [];
        if(isset($user->additional_groups)) {
            $additionalGroups = explode(',', $user->additional_groups);
            $groups = array_merge($groups, $additionalGroups);
        }
        $groups[] = $user->id_group;

        // add guest membergroup
        $groups[] = 0;

        $this->groups = $groups;
        return $groups;
    }

    /**
     * Returns whether the user is a staff member or admin
     *
     * @return bool
     */
    public function isStaff() : bool {
        if($this->isGuest()) {
            return false;
        }

        // determine if this user belongs to any group considered staff
        $groups = $this->getUserGroupsFromDatabase();
        foreach($groups as $group) {
            if(in_array($group->id_group, $this->staffGroups) != false) {
                return true;
            }
        }

        return false;
    }

}