<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\TwoFactorAuthException;

class TwoFactorSetupController extends Controller
{
    /**
     * Enables 2FA for the current user by generating them a 2FA secret
     *
     * @param Request $request
     * @param TwoFactorAuth $twoFactorAuth
     * @return JsonResponse
     * @throws TwoFactorAuthException
     */
    public function enable(Request $request, TwoFactorAuth $twoFactorAuth): JsonResponse
    {
        $user = $request->user();

        if ($user->two_factor_secret !== null) {
            abort(400, 'Two Factor Authentication is already enabled');
        }

        $user->two_factor_secret = encrypt($twoFactorAuth->createSecret());
        $user->save();

        return response()->json();
    }

    /**
     * Disables 2FA for the current user (provided that they can authorize it)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function disable(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->two_factor_secret === null) {
            abort(400, 'Two Factor Authentication is already disabled');
        }

        $request->validate([
           'password' => 'required',
        ]);
        if (! Hash::check($request->get('password'), $user->password)) {
            // TODO: rate limit this !!!
            abort(401, 'Invalid password');
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return response()->json();
    }

    /**
     * Generates a fresh set of recovery codes for the current user
     *
     * @param Request $request
     * @param TwoFactorAuth $twoFactorAuth
     * @return JsonResponse
     */
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

    /**
     * Completes 2FA setup for the current user (provided that they can give us
     * a valid 2FA code)
     *
     * @param Request $request
     * @param TwoFactorAuth $twoFactorAuth
     * @return JsonResponse
     * @throws ValidationException
     */
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
            secret: decrypt($user->two_factor_secret),
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

    /**
     * Returns image data to display a QR code. The QR code can be scanned by
     * the user in their 2FA app of choice to start generating 2FA codes.
     *
     * @param Request $request
     * @param TwoFactorAuth $twoFactorAuth
     * @return JsonResponse
     * @throws TwoFactorAuthException
     */
    public function qrCode(Request $request, TwoFactorAuth $twoFactorAuth): JsonResponse
    {
        $user = $request->user();

        if ($user->two_factor_secret === null) {
            abort(400, 'Two Factor Authentication must be enabled first');
        }
        return response()->json([
            'qr' => $twoFactorAuth->getQRCodeImageAsDataUri(
                label: $user->username,
                secret: decrypt($user->two_factor_secret),
            ),
        ]);
    }
}
