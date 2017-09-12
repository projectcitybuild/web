<?php
/**
 * Represents either a logged-in user or a guest
 *
 * @package App\Modules\Forums\Services\SMF
 */
namespace App\Modules\Forums\Services\SMF;

use App\Modules\Forums\Models\{ForumMembergroup, ForumUser};
use Illuminate\Support\Collection;

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
     * Model representation of a SMF group
     * 
     * @var ForumUser
     */
    private $forumGroupModel;

    /**
     * Array of groups the user belongs to
     *
     * @var array
     */
    private $groups;


    public function __construct(int $smfUserId, ForumUser $forumUserModel, ForumMembergroup $forumGroupModel, array $staffGroups) {
        $this->userId = $smfUserId;
        $this->forumUserModel = $forumUserModel;
        $this->forumGroupModel = $forumGroupModel;
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
     * @return Collection
     */
    public function getUserGroupsFromDatabase() : Collection {
        if(isset($this->groups)) {
            return $this->groups;
        }

        $user = $this->getUserfromDatabase();
        if(empty($user)) {
            return new Collection();
        }

        // combine primary and secondary groups
        $groups = [];
        if(isset($user->additional_groups)) {
            $additionalGroups = explode(',', $user->additional_groups);
            $groups = array_merge($groups, $additionalGroups);
        }
        $groups[] = $user->id_group;

        $this->groups = $this->forumGroupModel
            ->whereIn('id_group', $groups)
            ->get(['id_group', 'group_name', 'group_type', 'id_parent']);

        return $this->groups;
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