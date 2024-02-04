<?php

namespace App\Console\Commands;

use Entities\Models\Eloquent\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Library\Auditing\Causers\SystemCauser;
use Library\Auditing\Causers\SystemCauseResolver;

final class CleanupUnactivatedAccountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:unactivated-accounts
                        {--days= : Number of days to have elapsed since registration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes any accounts with an unactivated email address that are older than the given number of days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SystemCauseResolver::setCauser(SystemCauser::UNACTIVATED_CLEANUP);

        $elapsedDaysToDelete = $this->option('days')
            ?: config('registration.days_elapsed_until_unactivated_purge', 14);

        $thresholdDate = now()->subDays($elapsedDaysToDelete);

        Log::info('[Unactivated Accounts] Deleting unactivated accounts created before '.$thresholdDate);

        Account::where('activated', false)
            ->whereDate('updated_at', '<', $thresholdDate)
            ->delete();

        Log::info('[Unactivated Accounts] Done');
    }
}
