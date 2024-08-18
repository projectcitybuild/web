<?php

namespace App\Domains\Login\UseCases;

use Illuminate\Support\Facades\Auth;

class LogoutAccount
{
    /**
     * Invalidates a PCB session
     *
     * @return bool Whether logout was successful
     */
    public function execute(): bool
    {
        if (! Auth::check()) {
            return false;
        }
        Auth::logout();

        return true;
    }
}
