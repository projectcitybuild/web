<?php

namespace App\Http\Controllers\Auth;

use App\Domains\PasswordReset\Actions\SetNewPassword;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function __invoke(
        Request $request,
        SetNewPassword $setNewPassword,
    ): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        $setNewPassword->call(
            email: $validated['email'],
            password: $validated['password'],
            token: $validated['token'],
        );

        return response()->json();
    }
}
