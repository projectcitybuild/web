<?php
/**
 * Represents either a logged-in user or a guest
 *
 * @package App\Modules\Forums\Services\SMF
 */
namespace App\Modules\Forums\Services\SMF;

use App\Modules\Forums\Models\{ForumMembergroup, ForumUser};
use Support\Illumination\Collection;

class SmfUser {

    private $id;
    private $staffGroups;
    private $user;
    private $groups;

    public function __construct(int $smfUserId, array $staffGroups) {
        $this->id = $smfUserId;
        $this->staffGroups = $staffGroups;
    }

    /**
     * Returns whether the user is a guest or not
     *
     * @return bool
     */
    public function isGuest() : bool {
        return $data == null;
    }

    /**
     * Returns the user's forum account id
     *
     * @return int
     */
    public function getId() : int {
        return $this->isGuest() ? -1 : $this->id;
    }

    /**
     * Fetches a ForumUser of the user, if logged in
     *
     * @return ForumUser
     */
    public function getUserFromDatabase() : ForumUser {
        if(isset($this->user)) {
            return $this->user;
        }

        $this->user = ForumUser::where('id_member', $this->id)
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

        // combine primary and secondary groups
        if($user->additional_groups != null)
            $groups = explode(',', $user->additional_groups);
        $groups[] = $user->id_group;

        $this->groups = ForumMembergroup::whereIn('id_group', $groups)
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