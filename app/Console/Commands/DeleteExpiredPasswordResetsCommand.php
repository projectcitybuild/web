<?php

namespace App\Console\Commands;

use App\Entities\Models\Eloquent\Account;
use Domain\PasswordReset\UseCases\DeleteExpiredPasswordResetsUseCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class DeleteExpiredPasswordResetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:password-resets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old password reset requests';

    /**
     * Execute the console command.
     */
    public function handle(DeleteExpiredPasswordResetsUseCase $deleteExpiredPasswordResetsUseCase)
    {
        Log::info('Deleting expired password reset requests...');

        $deleteExpiredPasswordResetsUseCase->execute();

        Log::info('Deleted expired password reset requests');
    }
}
