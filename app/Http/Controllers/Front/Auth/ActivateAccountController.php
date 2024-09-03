<?php

namespace App\Http\Controllers\Front\Auth;

use App\Domains\Registration\Exceptions\AccountAlreadyActivatedException;
use App\Domains\Registration\UseCases\ActivateUnverifiedAccount;
use App\Domains\Registration\UseCases\ResendActivationEmail;
use App\Http\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ActivateAccountController extends WebController
{
    public function show(Request $request)
    {
        return view('front.pages.auth.activate.verify-mail')
            ->with('email', $request->user()->email);
    }

    public function activate(
        Request $request,
        ActivateUnverifiedAccount $activateUnverifiedAccount
    ): View|RedirectResponse {
        $email = $request->user()->email;

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

        return redirect()
            ->route('front.login')
            ->with('success', 'Your account has been activated! Please login below');
    }

    public function resendMail(
        Request $request,
        ResendActivationEmail $resendActivationEmail,
    ) {
        $email = $request->user()->email;

        try {
            $resendActivationEmail->execute(email: $email);
        } catch (AccountAlreadyActivatedException) {
            return redirect()->back()->withErrors([
                'Account has already been activated',
            ]);
        }

        return redirect()
            ->route('front.activate', ['email' => $email])
            ->with('success', 'An activation email has been sent to '.$email.'.');
    }
}
