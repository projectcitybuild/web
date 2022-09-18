<?php

namespace App\Console;

use App\Console\Commands\AccountCreatedAtImport;
use App\Console\Commands\CleanupExpiredPasswordResetsCommand;
use App\Console\Commands\CleanupUnactivatedAccountsCommand;
use App\Console\Commands\DeactivateDonatorPerksCommand;
use App\Console\Commands\DeactivateExpiredBansCommand;
use App\Console\Commands\GenerateScoutIndexesCommand;
use App\Console\Commands\GenerateSitemapCommand;
use App\Console\Commands\RepairMissingGroupsCommand;
use App\Console\Commands\RewardCurrencyToDonorsCommand;
use App\Console\Commands\ServerQueryCommand;
use App\Console\Commands\TestServiceCommand;
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
        DeactivateExpiredBansCommand::class,
        CleanupExpiredPasswordResetsCommand::class,
        GenerateSitemapCommand::class,
        RepairMissingGroupsCommand::class,
        RewardCurrencyToDonorsCommand::class,
        ServerQueryCommand::class,
        AccountCreatedAtImport::class,
        GenerateScoutIndexesCommand::class,
        TestServiceCommand::class,
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

        $schedule->command('horizon:snapshot')
            ->everyFiveMinutes();

        $schedule->command('passport:purge')
            ->hourly();

        $schedule->command('sitemap:generate')
            ->daily();

        $schedule->command('cleanup:password-resets')
            ->daily();

        $schedule->command('cleanup:unactivated-accounts')
            ->weekly();

        $schedule->command('donor-perks:expire')
            ->hourly();

        $schedule->command('donor-perks:reward-currency')
            ->hourly();

        $schedule->command('bans:expire')
            ->everyFifteenMinutes();

        if (! Environment::isLocalDev()) {
            $schedule->command('server:query --all --background')
                ->everyFiveMinutes();

            $schedule->command('backup:clean')
                ->dailyAt('00:00');

            $schedule->command('backup:run')
                ->dailyAt('01:00');

            $schedule->command('backup:monitor')
                ->dailyAt('02:00');
        }
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
