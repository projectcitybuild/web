<?php
namespace Interfaces\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Interfaces\Console\Commands\QueryServerCommand;
use Interfaces\Console\Commands\ImportCommand;
use Application\Environment;

class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        QueryServerCommand::class,
        ImportCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (Environment::isDev()) {
            return;
        }
        
        $schedule->command('query:status --all')
                 ->everyFiveMinutes();

        $schedule->command('cleanup:password-reset')
                ->weekly();

        $schedule->command('cleanup:unactivated-account')
                ->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('interfaces/console/Routes.php');
    }
}
