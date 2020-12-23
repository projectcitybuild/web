<?php

namespace App\Console;

use App\Console\Commands\CleanupUnactivatedAccountsCommand;
use App\Console\Commands\DeactivateDonatorPerksCommand;
use App\Console\Commands\ImportEssentialsBansCommand;
use App\Console\Commands\QueryServerCommand;
use App\Console\Commands\RepairMissingGroupsCommand;
use App\Console\Commands\ServerKeyCreateCommand;
use App\Console\Commands\StripUUIDHyphensCommand;
use App\Entities\Environment;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected array $commands = [
        QueryServerCommand::class,
        ServerKeyCreateCommand::class,
        StripUUIDHyphensCommand::class,
        RepairMissingGroupsCommand::class,
        ImportEssentialsBansCommand::class,
        CleanupUnactivatedAccountsCommand::class,
        DeactivateDonatorPerksCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        if (Environment::isDev()) {
            return;
        }

        $schedule->command('query:status --all')
            ->everyFiveMinutes();

        $schedule->command('cleanup:password-resets')
            ->weekly();

        $schedule->command('cleanup:unactivated-accounts')
            ->weekly();

        $schedule->command('donator-perks:expire')
            ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands(): void
    {
        require base_path('routes/console.php');
    }
}
