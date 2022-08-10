<?php

namespace App\Console;

use App\Console\Commands\CleanupUnactivatedAccountsCommand;
use App\Console\Commands\DeactivateDonatorPerksCommand;
use App\Console\Commands\DeleteExpiredPasswordResetsCommand;
use App\Console\Commands\GenerateSitemapCommand;
use App\Console\Commands\RepairMissingGroupsCommand;
use App\Console\Commands\RewardCurrencyToDonorsCommand;
use App\Console\Commands\ServerKeyCreateCommand;
use App\Console\Commands\ServerQueryCommand;
use App\Console\Commands\StripUUIDHyphensCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Library\Environment\Environment;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CleanupUnactivatedAccountsCommand::class,
        DeactivateDonatorPerksCommand::class,
        DeleteExpiredPasswordResetsCommand::class,
        GenerateSitemapCommand::class,
        RepairMissingGroupsCommand::class,
        RewardCurrencyToDonorsCommand::class,
        ServerKeyCreateCommand::class,
        ServerQueryCommand::class,
        StripUUIDHyphensCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('telescope:prune')
            ->daily();

        if (Environment::isLocalDev()) {
            return;
        }

        $schedule->command('server:query --all')
            ->everyFiveMinutes();

        $schedule->command('cleanup:password-resets')
            ->daily();

        $schedule->command('cleanup:unactivated-accounts')
            ->weekly();

        $schedule->command('donor-perks:expire')
            ->hourly();

        $schedule->command('donor-perks:reward-currency')
            ->hourly();

        $schedule->command('backup:clean')
            ->dailyAt('00:00');

        $schedule->command('backup:run')
            ->dailyAt('01:00');

        $schedule->command('backup:monitor')
            ->dailyAt('02:00');

        $schedule->command('passport:purge')
            ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
