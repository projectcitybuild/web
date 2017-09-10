<?php
/**
 * Represents either a logged-in user or a guest
 *
 * @package App\Modules\Forums\Services\SMF
 */
namespace App\Modules\Forums\Services\SMF;

use App\Modules\Forums\Models\{ForumMembergroup, ForumUser};
use Cache;

class SmfUser {

    private $id;
    private $staffGroups;
    private $user;
    private $groups;

    public function __construct($smfUserId) {
        $this->id = $smfUserId;
    }

    public function isGuest() : bool {
        return $data == null;
    }

    public function getId()
    {
        return $this->isGuest() ? -1 : $this->id;
    }

    public function FetchUser()
    {
        if($this->user != null)
            return $this->user;

        $this->user = ForumUser::where('id_member', $this->id)
            ->first();

        return $this->user;
    }

    public function FetchGroups()
    {
        if($this->groups != null)
            return $this->groups;

        $user = $this->FetchUser();

        // combine primary and secondary groups
        if($user->additional_groups != null)
            $groups = explode(',', $user->additional_groups);
        $groups[] = $user->id_group;

        $this->groups = Cache::remember('smf-user-' . $this->id . '-groups', 3, function() use($groups) {
            return ForumMembergroup::whereIn('id_group', $groups)
                ->get(['id_group', 'group_name', 'group_type', 'id_parent']);
        });

        return $this->groups;
    }

    public function IsStaff()
    {
        if($this->IsGuest())
            return false;

        // determine if this user belongs to any group considered staff
        $groups = $this->FetchGroups();
        foreach($groups as $group)
        {
            if(in_array($group->id_group, $this->staffGroups) != false)
                return true;
        }

        return false;
    }

}