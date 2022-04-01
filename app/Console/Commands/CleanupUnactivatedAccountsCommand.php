<?php

namespace App\Console\Commands;

use App\Entities\Models\Eloquent\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class CleanupUnactivatedAccountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:unactivated-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes any accounts not activated within a threshold';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletionThreshold = config('auth.unactivated_cleanup_days');
        $thresholdDate = now()->subDays($deletionThreshold);

        Log::info('[Unactivated Accounts] Deleting unactivated accounts unedited since '.$thresholdDate);

        Account::where('activated', false)
            ->whereDate('updated_at', '<', $thresholdDate)
            ->delete();

        Log::info('[Unactivated Accounts] Done');
    }
}
