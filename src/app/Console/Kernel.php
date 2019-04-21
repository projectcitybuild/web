<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\QueryServerCommand;
use App\Environment;
use App\Console\Commands\ImportGroupCommand;
use App\Console\Commands\GroupAddUserCommand;
use App\Console\Commands\GroupRemoveUserCommand;

class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        QueryServerCommand::class,
        ImportGroupCommand::class,
        GroupAddUserCommand::class,
        GroupRemoveUserCommand::class,
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
        // require base_path('app\Console\Routes.php');
    }
}
