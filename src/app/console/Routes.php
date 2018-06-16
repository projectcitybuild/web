<?php

use App\Modules\Accounts\Services\PasswordResetCleanupService;

/*
|--------------------------------------------------------------------------
| console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('cleanup:password-resets', function() {
    $cleanupService = resolve(PasswordResetCleanupService::class);
    $cleanupService->cleanup();

})->describe('Delete old password reset requests');