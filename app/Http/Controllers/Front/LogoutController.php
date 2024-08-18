<?php

namespace App\Http\Controllers\Front;

use App\Domains\Login\UseCases\LogoutAccount;
use App\Http\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LogoutController extends WebController
{
    public function __construct(
        private LogoutAccount $logoutUseCase,
    ) {
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->logoutUseCase->execute();

        // Prevent session fixation
        // https://laravel.com/docs/9.x/authentication#logging-out
        $request->session()->regenerateToken();

        return redirect()->route('front.home');
    }
}
