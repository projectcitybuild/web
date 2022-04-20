<?php

namespace App\Console\Commands;

use Domain\Donations\UseCases\DeactivateExpiredDonorPerksUseCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class DeactivateDonatorPerksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donor-perks:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivates donor perks if they have expired. Also removes the Donor group from the user if necessary';

    /**
     * Execute the console command.
     */
    public function handle(DeactivateExpiredDonorPerksUseCase $deactivateExpiredDonorPerks)
    {
        Log::info('Checking for expired donation perks');

        $deactivateExpiredDonorPerks->execute();
    }
}
