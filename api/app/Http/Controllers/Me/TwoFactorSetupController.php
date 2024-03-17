<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use RobThree\Auth\TwoFactorAuth;

class TwoFactorSetupController extends Controller
{
    public function enable(Request $request, TwoFactorAuth $twoFactorAuth): JsonResponse
    {
        $user = $request->user();

        if ($user->two_factor_secret !== null) {
            abort(400, 'Two Factor Authentication is already enabled');
        }

        $user->two_factor_secret = $twoFactorAuth->createSecret();
        $user->save();

        return response()->json();
    }

    public function disable(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->two_factor_secret === null) {
            abort(400, 'Two Factor Authentication is already disabled');
        }

        $request->validate([
           'password' => 'required',
        ]);
        $credentials = [
            'email' => $user->email,
            'password' => $request->get('password'),
        ];
        if (! Auth::validate($credentials)) {
            // TODO: rate limit this !!!
            throw new UnauthorizedException('Invalid password');
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return response()->json();
    }

    public function recoveryCodes(Request $request, TwoFactorAuth $twoFactorAuth): JsonResponse
    {
        $user = $request->user();

        if ($user->two_factor_secret === null) {
            abort(400, 'Two Factor Authentication must be enabled first');
        }

        $generator = fn () => Str::random(10).'-'.Str::random(10);
        $codes = Collection::times(8, $generator)->all();

        $user->two_factor_recovery_codes = encrypt(json_encode($codes));
        $user->save();

        return response()->json([
            'recovery_codes' => $codes,
        ]);
    }

    public function confirm(Request $request, TwoFactorAuth $twoFactorAuth): JsonResponse
    {
        $user = $request->user();

        if ($user->two_factor_secret === null) {
            abort(400, 'Two Factor Authentication must be enabled first');
        }
        if ($user->two_factor_confirmed_at !== null) {
            abort(409, 'Two Factor Authentication has already been confirmed');
        }

        $request->validate([
           'code' => ['required', 'string'],
        ]);
        $isValid = $twoFactorAuth->verifyCode(
            secret: $user->two_factor_secret,
            code: $request->get('code'),
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

    public function qrCode(Request $request, TwoFactorAuth $twoFactorAuth): JsonResponse
    {
        $user = $request->user();

        if ($user->two_factor_secret === null) {
            abort(400, 'Two Factor Authentication must be enabled first');
        }

        // TODO: what is the label for?
        return response()->json([
            'qr' => $twoFactorAuth->getQRCodeImageAsDataUri($user->username, secret: $user->two_factor_secret),
        ]);
    }
}
