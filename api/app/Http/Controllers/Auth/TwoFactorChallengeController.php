<?php

namespace App\Http\Controllers\Auth;

use App\Domains\MFA\Actions\VerifyMFACode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TwoFactorChallengeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TwoFactorChallengeController extends Controller
{
    public function __invoke(
        TwoFactorChallengeRequest $request,
        VerifyMFACode $verifyMFACode,
    ): JsonResponse
    {
        $validated = $request->validated();

        $isValid = $verifyMFACode->call(
            secret: $request->user()->two_factor_secret,
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
