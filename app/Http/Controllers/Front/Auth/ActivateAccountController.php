<?php

namespace App\Http\Controllers\Front\Auth;

use App\Domains\Activation\Exceptions\AccountAlreadyActivatedException;
use App\Domains\Activation\UseCases\ActivateUnverifiedAccount;
use App\Domains\Activation\UseCases\SendActivationEmail;
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
        string $token,
        ActivateUnverifiedAccount $activateAccount,
    ): View|RedirectResponse {
        $account = $request->user();
        if ($account === null) {
            abort(401);
        }

        try {
            $activateAccount->execute(
                account: $account,
                token: $token,
            );
        } catch (AccountAlreadyActivatedException) {
            // Ignore
        }
        return redirect()
            ->intended(route('front.account.profile'))
            ->with('success', 'Your account has been activated');
    }

    public function resendMail(
        Request $request,
        SendActivationEmail $sendActivationEmail,
    ) {
        $account = $request->user();
        if ($account === null) {
            abort(401);
        }

        try {
            $sendActivationEmail->execute($account);
        } catch (AccountAlreadyActivatedException) {
            return redirect()->intended(route('front.account.profile'));
        }

        return redirect()
            ->route('front.activate')
            ->with('success', 'An activation email has been sent to '.$account->email.'.');
    }
}
