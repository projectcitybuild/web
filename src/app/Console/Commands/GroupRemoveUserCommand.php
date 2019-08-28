<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Entities\Eloquent\Groups\GroupEnum;
use App\Entities\Eloquent\Groups\Repositories\GroupRepository;
use App\Services\Groups\DiscourseGroupSyncService;
use App\Entities\Eloquent\Accounts\Repositories\AccountRepository;
use App\Entities\Requests\Discourse\DiscourseUserFetchAPIRequest;
use App\Library\APIClient\APIClient;

class GroupRemoveUserCommand extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:deluser {group} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes a user (by email) to a group';

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var APIClient
     */
    private $apiClient;

    /**
     * @var DiscourseGroupSyncService
     */
    private $groupSyncService;


    public function __construct(
        AccountRepository $accountRepository,
        GroupRepository $groupRepository,
        APIClient $apiclient,
        DiscourseGroupSyncService $groupSyncService
    ) {
        parent::__construct();

        $this->accountRepository = $accountRepository;
        $this->groupRepository = $groupRepository;
        $this->apiClient = $apiclient;
        $this->groupSyncService = $groupSyncService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $group = $this->argument('group');
        $email = $this->argument('email');

        $rawGroup = null;
        try {
            $rawGroup = GroupEnum::fromRawValue($group);
        } catch(\Exception $e) {
            $this->error('Invalid group name');
            return;
        }

        $account = $this->accountRepository->getByEmail($email);
        if ($account === null) {
            $this->error('User with that email address does not exist');
            return;
        }

        $pcbGroup = $this->groupRepository->getGroupByName($rawGroup);
        $pcbGroupId = $pcbGroup->getKey();
        
        if ($account->groups->contains($pcbGroupId) === false) {
            $this->error('User does not belongs to group '.$pcbGroup->name);
            return;
        }

        $request = new DiscourseUserFetchAPIRequest($account->getKey());
        $discourseUser = $this->apiClient->request($request);
        $discourseId = $discourseUser['user']['id'];

        $this->groupSyncService->removeUserFromGroup($discourseId, $account, $rawGroup);
        $this->info('Removed user from group '.$pcbGroup->name);
    }
}