<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use Domains\Library\Discourse\Api\DiscourseUserApi;
use Domains\Library\Discourse\Api\DiscourseGroupApi;
use Domains\Library\Discourse\Api\DiscourseAdminApi;
use Illuminate\Support\Facades\DB;
use Domains\Library\Discourse\Entities\DiscoursePayload;
use Cache;

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

        $importedDiscourseIds = Cache::get('import-groups', []);

        $this->info('Importing '.$discourseGroupName);
        $group = $groupApi->fetchGroupMembers($discourseGroupName, 300);
        $members = $group['members'];
        
        $bar = $this->output->createProgressBar(count($members));

        foreach ($members as $member) {
            $discourseId = $member['id'];
            if (in_array($discourseId, $importedDiscourseIds) === true) {
                $bar->advance();
                continue;
            }

            $user = $adminApi->fetchUserByDiscourseId($discourseId, true);
            $sso = $user['single_sign_on_record'];

            $account = null;
            if ($sso !== null) {
                $pcbId = $sso['external_id'];
                $account = Account::where('account_id', $pcbId)->first();

            } else {
                $emails = $adminApi->fetchEmailsByUsername($user['username']);
                $email = @$emails['email'];
                if ($email === null) {
                    $this->error('No SSO record or email for '. $user['username']);
                    $bar->advance();
                    $importedDiscourseIds[] = $discourseId;
                    Cache::put('import-groups', $importedDiscourseIds, 360);
                    continue;
                }

                $account = Account::where('email', $email)->first();
                if ($account === null) {
                    $this->error('No matching PCB email for '.$user['username'].': '. $user['email']);
                    $bar->advance();
                    $importedDiscourseIds[] = $discourseId;
                    Cache::put('import-groups', $importedDiscourseIds, 360);
                    continue;

                } else {
                    $this->info('Found email for '.$user['username']);
                }
            }

            $account->groups()->attach($pcbGroupId);

            $importedDiscourseIds[] = $discourseId;
            Cache::put('import-groups', $importedDiscourseIds, 360);
            $bar->advance();
        }
    }
}