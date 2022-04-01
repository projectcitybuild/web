<?php

namespace App\Http\Controllers;

use App\Entities\Models\Eloquent\Group;
use App\Entities\Notifications\AccountActivationNotification;
use App\Entities\Repositories\AccountRepository;
use App\Http\Actions\AccountRegistration\ActivateUnverifiedAccount;
use App\Http\Requests\RegisterRequest;
use App\Http\WebController;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class RegisterController extends WebController
{
    public function showRegisterView(Request $request)
    {
        if ($request->session()->has('url.intended')) {
            return response()->view('front.pages.register.register')->cookie(
                'intended',
                $request->session()->get('url.intended'),
                60);
        }

        return view('v2.front.pages.register.register');
    }

    public function register(RegisterRequest $request, AccountRepository $accountRepository)
    {
        $input = $request->validated();

        $account = $accountRepository->create($input['email'], $input['username'], $input['password'], $request->ip());

        $defaultGroups = Group::where('is_default', 1)->pluck('group_id');

        $account->groups()->attach($defaultGroups);

        $account->notify(new AccountActivationNotification($account));

        return view('v2.front.pages.register.register-success');
    }

    /**
     * Attempts to activate an account via token.
     *
     *
     *
     * @return View
     *
     * @throws \Exception
     */
    public function activate(Request $request, ActivateUnverifiedAccount $activateUnverifiedAccount)
    {
        $email = $request->get('email');

        if (empty($email)) {
            return redirect()->route('front.register');
        }

        $activateUnverifiedAccount->execute(
            $email,
            $request->ip()
        );

        if ($request->session()->has('url.intended')) {
            $intended = $request->session()->get('url.intended');
            $request->session()->remove('intended');

            return redirect($intended);
        }

        return view('v2.front.pages.register.register-verify-complete');
    }
}
