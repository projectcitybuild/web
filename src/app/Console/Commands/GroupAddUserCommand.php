<?php
namespace Interfaces\Console\Commands;

use Illuminate\Console\Command;
use App\Entities\Groups\GroupEnum;
use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Repositories\GroupRepository;
use Domains\Services\Groups\DiscourseGroupSyncService;
use Domains\Library\Discourse\Api\DiscourseAdminApi;
use Domains\Library\Discourse\Api\DiscourseUserApi;
use App\Entities\Accounts\Repositories\AccountRepository;

class GroupAddUserCommand extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:adduser {group} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a user (by email) to a group';

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var DiscourseUserApi
     */
    private $userApi;

    /**
     * @var DiscourseGroupSyncService
     */
    private $groupSyncService;


    public function __construct(
        AccountRepository $accountRepository,
        GroupRepository $groupRepository,
        DiscourseUserApi $userApi,
        DiscourseGroupSyncService $groupSyncService
    ) {
        parent::__construct();

        $this->accountRepository = $accountRepository;
        $this->groupRepository = $groupRepository;
        $this->userApi = $userApi;
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
        
        if ($account->groups->contains($pcbGroupId)) {
            $this->error('User already belongs to group '.$pcbGroup->name);
            return;
        }

        $discourseUser = $this->userApi->fetchUserByPcbId($account->getKey());
        $discourseId = $discourseUser['user']['id'];

        $this->groupSyncService->addUserToGroup($discourseId, $account, $rawGroup);
        $this->info('Added user to group '.$pcbGroup->name);
    }
}