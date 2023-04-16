<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    /**
     * @throws ValidationException if email and/or password is incorrect
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! auth()->attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Email or password is incorrect'],
            ]);
        }

        $account = auth()->user();

        if (! $account->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Account not verified. Please check the email sent to your email address'],
            ]);
        }

        $account->tokens()->delete();
        $account->createToken($request->email)->plainTextToken;

        return response()->json([
            'account' => $account,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $account = auth()->user();
        $account->tokens()->delete();

        return response()->json();
    }
}
