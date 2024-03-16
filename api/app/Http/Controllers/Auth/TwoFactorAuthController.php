<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RobThree\Auth\TwoFactorAuth;

class TwoFactorAuthController extends Controller
{
    public function enable(Request $request, TwoFactorAuth $twoFactorAuth): JsonResponse
    {
        $secret = $twoFactorAuth->createSecret();

        $user = $request->user();
        $user->two_factor_secret = $secret;
        $user->save();

        return response()->json();
    }

    public function disable(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return response()->json();
    }

    public function recoveryCodes(Request $request, TwoFactorAuth $twoFactorAuth): JsonResponse
    {
        $user = $request->user();

        // TODO: replace this with a regular Hash token?
        $code = $twoFactorAuth->getCode(secret: $user->two_factor_secret);

        $user->two_factor_recovery_codes = $code;
        $user->save();

        return response()->json([
            "recovery_codes" => [$code],
        ]);
    }

    public function confirm(Request $request, TwoFactorAuth $twoFactorAuth): JsonResponse
    {
        $request->validate([
           'code' => ['required', 'string'],
        ]);
        $code = $request->get('code');
        $user = $request->user();

        $isValid = $twoFactorAuth->verifyCode(
            secret: $user->two_factor_secret,
            code: $code,
        );
        if (! $isValid) {
            throw ValidationException::withMessages([
               'code' => 'Invalid two factor authentication code'
            ]);
        }

        $user->two_factor_confirmed_at = now();
        $user->save();

        return response()->json();
    }

    public function qrCode(Request $request, TwoFactorAuth $twoFactorAuth)
    {
        $user = $request->user();

        // TODO: what is the label for?
        return $twoFactorAuth->getQRCodeImageAsDataUri('PCB', secret: $user->two_factor_secret);
    }
}
