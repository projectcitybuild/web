<?php

namespace App\Console\Commands;

use Domain\Bans\UseCases\ExpireBans;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class DeactivateExpiredBansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bans:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivates any temporary bans that have already expired';

    /**
     * Execute the console command.
     */
    public function handle(ExpireBans $expireBans)
    {
        Log::info('Deactivating expired temporary bans...');

        $expireBans->execute();

        Log::info('Deactivated expired temporary bans');
    }
}
