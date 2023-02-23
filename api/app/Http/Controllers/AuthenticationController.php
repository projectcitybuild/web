<?php

namespace App\Http\Controllers;

use App\Models\Eloquent\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    /**
     * @throws ValidationException if email and/or password is incorrect
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // TODO: use Auth instead?
        $account = Account::where('email', $request->email)->first();
        if ($account === null) {
            throw ValidationException::withMessages([
                'email' => ['Email or password is incorrect'],
            ]);
        }

        $account->tokens()->delete();

        if (! Hash::check($request->password, $account->password)) {
            throw ValidationException::withMessages([
               'email' => ['Email or password is incorrect'],
            ]);
        }

        $token = $account->createToken($request->email)->plainTextToken;

        return response()->json([
            'auth_token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $account = auth()->user();
        $account->tokens()->delete();

        return response()->json();
    }
}
