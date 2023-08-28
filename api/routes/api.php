<?php

use App\Http\Controllers\Account\MinecraftLinkController;
use App\Http\Controllers\Account\UpdatePasswordController;
use App\Http\Controllers\Account\UpdateUsernameController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Donations\DonationController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;


Route::middleware(['guest', 'throttle:6,1'])->group(function () {
    Route::post('/register', [RegistrationController::class, 'register']);
});

$authMiddleware = ['auth:sanctum', 'verified'];

Route::middleware($authMiddleware)->group(function() {
    Route::get('/profile/me', [AccountController::class, 'me']);

    Route::put('/account/password', [UpdatePasswordController::class, 'update']);
    Route::post('/account/email', [UpdatePasswordController::class, 'store']);
    Route::patch('/account/email', [UpdatePasswordController::class, 'update']);
    Route::patch('/account/username', [UpdateUsernameController::class, 'update']);
    Route::patch('/account/link/minecraft', [MinecraftLinkController::class, 'store']);
    Route::get('/account/donations', [DonationController::class, 'index']);

    Route::apiResource('/servers', ServerController::class);
    Route::apiResource('/groups', GroupController::class);
});
