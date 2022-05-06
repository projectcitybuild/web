<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use Domain\Login\UseCases\LogoutUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LogoutController extends WebController
{
    public function __construct(
        private LogoutUseCase $logoutUseCase,
    ) {}

    public function logout(Request $request): RedirectResponse
    {
        $this->logoutUseCase->logoutOfPCB();

        // Prevent session fixation
        // https://laravel.com/docs/9.x/authentication#logging-out
        $request->session()->regenerateToken();

        return redirect()->route('front.home');
    }
}
