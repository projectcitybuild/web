<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use Illuminate\Support\Facades\DB;

final class RepairMissingGroupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'groups:assign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds any accounts without a group assigned, and assigns them to the default groups';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $accountsWithoutGroups = Account::doesntHave('groups')->get();

        if (count($accountsWithoutGroups) === 0) {
            $this->info('No accounts need to be assigned a group');
            return;   
        }

        $defaultGroupIds = Group::where('is_default', 1)->get()->pluck('group_id');

        $progressBar = $this->output->createProgressBar(count($accountsWithoutGroups));
        $progressBar->start();
     
        DB::transaction(function() use($accountsWithoutGroups, $defaultGroupIds, &$progressBar) {
            foreach ($accountsWithoutGroups as $account) {
                $account->groups()->attach($defaultGroupIds);
                $progressBar->advance();
            }
        });

        $this->info(count($accountsWithoutGroups) . ' accounts assigned to a default group');

        $progressBar->finish();
    }
}