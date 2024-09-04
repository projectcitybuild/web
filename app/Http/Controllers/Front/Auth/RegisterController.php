<?php

namespace App\Http\Controllers\Front\Auth;

use App\Domains\Registration\UseCases\CreateUnactivatedAccount;
use App\Http\Controllers\WebController;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function register(
        RegisterRequest $request,
        CreateUnactivatedAccount $createUnactivatedAccount,
    ): RedirectResponse {
        $validated = $request->validated();

        $createUnactivatedAccount->execute(
            email: $validated['email'],
            username: $validated['username'],
            password: $validated['password'],
            ip: $request->ip(),
        );

        return redirect()->route('front.activate');
    }
}
