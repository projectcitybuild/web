<?php

namespace App\Http\Controllers;

use App\Http\Actions\AccountRegistration\SendRegisterVerificationEmail;
use App\Http\Actions\AccountRegistration\ActivateUnverifiedAccount;
use App\Http\Requests\RegisterRequest;
use App\Http\WebController;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class RegisterController extends WebController
{
    public function showRegisterView()
    {
        return view('front.pages.register.register');
    }

    public function register(RegisterRequest $request, SendRegisterVerificationEmail $sendVerificationEmail)
    {
        $input = $request->validated();

        $sendVerificationEmail->execute(
            $input['email'],
            $input['username'],
            $input['password']
        );

        return view('front.pages.register.register-success');
    }

    /**
     * Attempts to activate an account via token
     *
     * @param Request $request
     *
     * @return View
     * @throws \Exception
     */
    public function activate(Request $request, ActivateUnverifiedAccount $activateUnverifiedAccount)
    {
        $email = $request->get('email');

        if (empty($email)) {
            return view('front.pages.register.register');
        }

        $activateUnverifiedAccount->execute(
            $email,
            $request->ip()
        );

        return view('front.pages.register.register-verify-complete');
    }
}
