<?php

namespace App\Http\Controllers\Front\Auth;

use App\Domains\Registration\UseCases\CreateUnactivatedAccount;
use App\Http\Controllers\WebController;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class RegisterController extends WebController
{
    public function show(Request $request): View|Response
    {
        $response = response()
            ->view('front.pages.auth.register.form');

        if ($request->session()->has('url.intended')) {
            $response->cookie('intended', $request->session()->get('url.intended'), 60);
        }

        return $response;
    }

    public function store(
        RegisterRequest $request,
        CreateUnactivatedAccount $createUnactivatedAccount,
    ): RedirectResponse {
        $validated = $request->validated();

        $account = $createUnactivatedAccount->execute(
            email: $validated['email'],
            username: $validated['username'],
            password: $validated['password'],
            ip: $request->ip(),
            lastLoginAt: Carbon::now(),
        );

        Auth::login($account);

        return redirect()->route('front.activate');
    }
}
