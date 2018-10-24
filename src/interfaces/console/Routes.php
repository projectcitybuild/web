<?php

use Domains\Services\PasswordReset\PasswordResetCleanupService;
use Domains\Services\Registration\UnactivatedAccountCleanupService;

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

Artisan::command('cleanup:password-resets', function () {
    $cleanupService = resolve(PasswordResetCleanupService::class);
    $cleanupService->cleanup();
})->describe('Delete old password reset requests');


Artisan::command('cleanup:unactivated-accounts', function () {
    $cleanupService = resolve(UnactivatedAccountCleanupService::class);
    $cleanupService->cleanup();
})->describe('Delete old unactivated accounts');
