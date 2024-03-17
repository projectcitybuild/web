<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TwoFactorChallengeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use RobThree\Auth\TwoFactorAuth;

class TwoFactorChallengeController extends Controller
{
    public function __invoke(TwoFactorChallengeRequest $request, TwoFactorAuth $twoFactorAuth): JsonResponse
    {
        $validated = $request->validated();

        $user = $request->user();

        $isValid = $twoFactorAuth->verifyCode(
            secret: decrypt($user->two_factor_secret),
            code: $validated['code'],
        );

        if (! $isValid) {
            throw ValidationException::withMessages([
               'code' => 'Invalid two factor authentication code'
            ]);
        }

        return response()->json();
    }
}
