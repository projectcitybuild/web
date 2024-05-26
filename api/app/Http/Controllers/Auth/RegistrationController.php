<?php

namespace App\Http\Controllers\Auth;

use App\Domains\Registration\Actions\CreateAccount;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationRequest;
use Illuminate\Http\JsonResponse;

class RegistrationController extends Controller
{
    public function __invoke(
        RegistrationRequest $request,
        CreateAccount $createAccount,
    ): JsonResponse
    {
        $validated = $request->safe()->collect();

        $createAccount->call(
            username: $validated->get('username'),
            email: $validated->get('email'),
            password: $validated->get('password'),
        );
        return response()->json();
    }
}
