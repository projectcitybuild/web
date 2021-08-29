<?php

namespace App\Console\Commands;

use Domain\Donations\DeactivateExpiredDonorPerks;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class DeactivateDonatorPerksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donator-perks:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivates donator perks if they have expired. Also removes the Donor group from the user if necessary';

    /**
     * Execute the console command.
     */
    public function handle(DeactivateExpiredDonorPerks $deactivateExpiredDonorPerks)
    {
        Log::info('Checking for expired donation perks');

        $deactivateExpiredDonorPerks->execute();
    }
}
