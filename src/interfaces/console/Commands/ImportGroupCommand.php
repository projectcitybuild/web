<?php
namespace Interfaces\Console\Commands;

use Illuminate\Console\Command;
use Domains\Modules\Accounts\Models\Account;
use Domains\Modules\Groups\Models\Group;
use Domains\Library\Discourse\Api\DiscourseUserApi;
use Domains\Library\Discourse\Api\DiscourseGroupApi;
use Domains\Library\Discourse\Api\DiscourseAdminApi;
use Illuminate\Support\Facades\DB;
use Domains\Library\Discourse\Entities\DiscoursePayload;

class ImportGroupCommand extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $accounts = Account::all();
        $api = resolve(DiscourseUserApi::class);

        $adminGroupId = Group::where('name', 'Administrator')->first()->getKey();
        $sopGroupId = Group::where('name', 'Senior Operator')->first()->getKey();
        $opGroupId = Group::where('name', 'Operator')->first()->getKey();
        $modGroupId = Group::where('name', 'Moderator')->first()->getKey();
        $trustedGroupId = Group::where('name', 'Trusted')->first()->getKey();
        $donatorGroupId = Group::where('name', 'Donator')->first()->getKey();
     
        $this->importGroup('administrator', $adminGroupId);
        $this->importGroup('senior-operator', $sopGroupId);
        $this->importGroup('operator', $opGroupId);
        $this->importGroup('moderator', $modGroupId);
        $this->importGroup('donator', $donatorGroupId);
        $this->importGroup('trusted', $trustedGroupId);
    }

    private function importGroup($discourseGroupName, $pcbGroupId) {
        $groupApi = resolve(DiscourseGroupApi::class);
        $adminApi = resolve(DiscourseAdminApi::class);

        $this->info('Importing '.$discourseGroupName);
        $group = $groupApi->fetchGroupMembers($discourseGroupName, 300);
        $members = $group['members'];
        
        $bar = $this->output->createProgressBar(count($members));

        foreach ($members as $member) {
            $discourseId = $member['id'];
            $user = $adminApi->fetchUserByDiscourseId($discourseId);
            $sso = $user['single_sign_on_record'];
            $pcbId = $sso['external_id'];
            dump($user);

            $account = Account::where('account_id', $pcbId)->first();
            $account->groups()->attach($pcbGroupId);

            $bar->advance();
        }
    }
}