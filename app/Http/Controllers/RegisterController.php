<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\WebController;
use Domain\SignUp\Exceptions\AccountAlreadyActivatedException;
use Domain\SignUp\UseCases\ActivateUnverifiedAccount;
use Domain\SignUp\UseCases\CreateUnactivatedAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

final class RegisterController extends WebController
{
    public function show(Request $request): View|Response
    {
        if ($request->session()->has('url.intended')) {
            return response()
                ->view('front.pages.register.register')
                ->cookie('intended', $request->session()->get('url.intended'), 60);
        }

        return view('front.pages.register.register');
    }

    public function register(
        RegisterRequest $request,
        CreateUnactivatedAccount $createUnactivatedAccountUseCase,
    ): View {
        $input = $request->validated();

        $createUnactivatedAccountUseCase->execute(
            email: $input['email'],
            username: $input['username'],
            password: $input['password'],
            ip: $request->ip(),
        );

        return view('front.pages.register.register-success');
    }

    public function activate(
        Request $request,
        ActivateUnverifiedAccount $activateUnverifiedAccount
    ): View|RedirectResponse {
        $email = $request->get('email');

        try {
            $activateUnverifiedAccount->execute(email: $email);
        } catch (AccountAlreadyActivatedException) {
            abort(code: 410, message: 'Account already activated');
        }

        if ($request->session()->has('url.intended')) {
            $intended = $request->session()->get('url.intended');
            $request->session()->remove('intended');

            return redirect($intended);
        }

        return view('front.pages.register.register-verify-complete');
    }
}
