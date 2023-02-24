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
        $validated = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! auth()->attempt($validated)) {
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
        $token = $account->createToken($request->email)->accessToken;

        return response()->json([
            'account' => $account,
            'access_token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $account = auth()->user();
        $account->tokens()->delete();

        return response()->json();
    }
}
