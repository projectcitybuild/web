<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RobThree\Auth\TwoFactorAuth;

class TwoFactorChallengeController extends Controller
{
    public function __invoke(Request $request, TwoFactorAuth $twoFactorAuth): JsonResponse
    {
        $user = $request->user();

        if (! $user->isTwoFactorEnabled()) {
            abort(400, 'Two Factor Authentication is not setup');
        }

        $request->validate([
           'code' => ['required', 'string'],
        ]);
        $isValid = $twoFactorAuth->verifyCode(
            secret: decrypt($user->two_factor_secret),
            code: $request->get('code'),
        );

        if (! $isValid) {
            throw ValidationException::withMessages([
               'code' => 'Invalid two factor authentication code'
            ]);
        }

        return response()->json();
    }
}
