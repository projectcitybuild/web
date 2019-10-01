<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Entities\Accounts\Models\Account;
use Illuminate\Support\Facades\DB;

final class RepairMissingGroupsCOmmand extends Command
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
        $defaultGroups = Group::where('is_default', 1)->get();

        $progressBar = $this->output->createProgressBar(count($accountsWithoutGroups));
        $progressBar->start();
     
        DB::transaction(function() use($accountsWithoutGroups, &$progressBar) {
            foreach ($accountsWithoutGroups as $account) {
                $account->groups()->attach($defaultGroups->pluck('group_id'));
                $progressBar->advance();
            }
        });

        $this->info(count($accountsWithoutGroups) . ' accounts assigned to a default group');

        $progressBar->finish();
    }
}