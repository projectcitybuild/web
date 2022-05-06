<?php

namespace Domain\Login\UseCases;

use Illuminate\Support\Facades\Auth;

class LogoutUseCase
{
    /**
     * Invalidates a PCB session
     *
     * @return bool Whether logout was successful
     */
    public function logoutOfPCB(): bool
    {
        if (! Auth::check()) {
            return false;
        }
        Auth::logout();

        return true;
    }
}
