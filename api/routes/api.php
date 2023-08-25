<?php

use App\Http\Controllers\Account\UpdatePasswordController;
use App\Http\Controllers\Account\UpdateUsernameController;
use App\Http\Controllers\Donations\DonationController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;


Route::middleware(['guest', 'throttle:6,1'])->group(function () {
    Route::post('/register', [RegistrationController::class, 'register']);
});

$authMiddleware = ['auth:sanctum', 'verified'];

Route::get('/profile/me', [AccountController::class, 'me'])
    ->middleware($authMiddleware);

Route::put('/account/password', [UpdatePasswordController::class, 'update'])
    ->middleware($authMiddleware);

Route::patch('/account/username', [UpdateUsernameController::class, 'update'])
    ->middleware($authMiddleware);

Route::get('/donations', [DonationController::class, 'index'])
    ->middleware($authMiddleware);

Route::get('/servers', [ServerController::class, 'index']);

Route::apiResource('/servers', ServerController::class)
    ->middleware($authMiddleware)
    ->except('index');

Route::apiResource('/groups', GroupController::class)
    ->middleware($authMiddleware);
