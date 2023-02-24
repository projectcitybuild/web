<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthenticationController::class, 'login']);

Route::post('/register', [RegistrationController::class, 'register']);

Route::prefix('/email/verify')->middleware('throttle:6,1')->group(function () {
    Route::get('/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->name('verification.verify')
        ->middleware(['signed']);

    Route::post('/resend', [VerifyEmailController::class, 'resend'])
        ->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    Route::apiResource('/servers', ServerController::class);
    Route::apiResource('/groups', GroupController::class);
});
