<?php
namespace App\Services\Groups;

use App\Library\Discourse\Api\DiscourseAdminApi;
use App\Entities\Eloquent\Groups\GroupEnum;
use App\Entities\Eloquent\Groups\Repositories\GroupRepository;
use App\Entities\Eloquent\Accounts\Models\Account;


final class DiscourseGroupSyncService {

    /**
     * @var DiscourseAdminApi
     */
    private $adminApi;

    /**
     * @var GroupRepository
     */
    private $groupRepository;


    public function __construct(
        DiscourseAdminApi $adminApi, 
        GroupRepository $groupRepository
    ) {
        $this->adminApi = $adminApi;
        $this->groupRepository = $groupRepository;
    }

    private function getPCBGroupId(GroupEnum $group) : int 
    {
        $pcbGroup = $this->groupRepository->getGroupByName($group->valueOf());
        if ($pcbGroup === null) {
            throw new \Exception('PCB group ['.$group->valueOf().'] does not exist');
        }
        return $pcbGroup->getKey();
    }

    /**
     * Adds an account to a PCB group and its associated Discourse group
     *
     * @param integer $discourseId
     * @param Account $account
     * @param GroupEnum $group
     * @return void
     */
    public function addUserToGroup(int $discourseId, Account $account, GroupEnum $group) 
    {
        $pcbGroupId = $this->getPCBGroupId($group);

        $discourseGroupId = $group->discourseId();
        $this->adminApi->addUserToGroup($discourseId, $discourseGroupId);

        $account->groups()->attach($pcbGroupId);
    }

    /**
     * Removes an account from a PCB group and its associated Discourse group 
     *
     * @param integer $discourseId
     * @param Account $account
     * @param GroupEnum $group
     * @return void
     */
    public function removeUserFromGroup(int $discourseId, Account $account, GroupEnum $group)
    {
        $pcbGroupId = $this->getPCBGroupId($group);

        $discourseGroupId = $group->discourseId();
        $this->adminApi->removeUserFromGroup($discourseId, $discourseGroupId);

        $account->groups()->detach($pcbGroupId);
    }

}