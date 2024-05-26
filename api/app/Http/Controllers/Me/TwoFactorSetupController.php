<?php

namespace App\Http\Controllers\Me;

use App\Domains\MFA\Actions\ConfirmMFASetup;
use App\Domains\MFA\Actions\CreateQRCode;
use App\Domains\MFA\Actions\CreateRecoveryCodes;
use App\Domains\MFA\Actions\DisableMFA;
use App\Domains\MFA\Actions\EnableMFA;
use App\Domains\MFA\Actions\VerifyMFACode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Me\TwoFactorConfirmRequest;
use App\Http\Requests\Me\TwoFactorDisableRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RobThree\Auth\TwoFactorAuthException;

class TwoFactorSetupController extends Controller
{
    /**
     * Enables 2FA for the current user by generating them a 2FA secret
     *
     * @param Request $request
     * @param EnableMFA $enableMFA
     * @return JsonResponse
     * @throws TwoFactorAuthException
     */
    public function enable(
        Request $request,
        EnableMFA $enableMFA,
    ): JsonResponse
    {
        $enableMFA->call(account: $request->user());

        return response()->json();
    }

    /**
     * Disables 2FA for the current user (provided that they can authorize it)
     *
     * @param TwoFactorDisableRequest $request
     * @param DisableMFA $disableMFA
     * @return JsonResponse
     */
    public function disable(
        TwoFactorDisableRequest $request,
        DisableMFA $disableMFA,
    ): JsonResponse
    {
        $request->validated();

        $disableMFA->call(account: $request->user());

        return response()->json();
    }

    /**
     * Generates a fresh set of recovery codes for the current user
     *
     * @param Request $request
     * @param CreateRecoveryCodes $createCodes
     * @return JsonResponse
     */
    public function recoveryCodes(
        Request $request,
        CreateRecoveryCodes $createCodes,
    ): JsonResponse
    {
        $codes = $createCodes->call(account: $request->user());

        return response()->json([
            'recovery_codes' => $codes,
        ]);
    }

    /**
     * Completes 2FA setup for the current user (provided that they can give us
     * a valid 2FA code)
     *
     * @param TwoFactorConfirmRequest $request
     * @param VerifyMFACode $verifyMFACode
     * @return JsonResponse
     * @throws ValidationException
     */
    public function confirm(
        TwoFactorConfirmRequest $request,
        VerifyMFACode $verifyMFACode,
        ConfirmMFASetup $confirmMFASetup,
    ): JsonResponse
    {
        $validated = $request->validated();
        $account = $request->user();

        $isValid = $verifyMFACode->call(
            secret: $account->two_factor_secret,
            code: $validated['code'],
        );
        if (! $isValid) {
            throw ValidationException::withMessages([
               'code' => 'Invalid two factor authentication code'
            ]);
        }

        $confirmMFASetup->call(account: $account);

        return response()->json();
    }

    /**
     * Returns image data to display a QR code. The QR code can be scanned by
     * the user in their 2FA app of choice to start generating 2FA codes.
     *
     * @param Request $request
     * @param CreateQRCode $createQRCode
     * @return JsonResponse
     * @throws TwoFactorAuthException
     */
    public function qrCode(
        Request $request,
        CreateQRCode $createQRCode,
    ): JsonResponse
    {
        $qr =  $createQRCode->call(account: $request->user());

        return response()->json([
            'qr' => $qr,
        ]);
    }
}
