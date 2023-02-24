<?php

namespace App\Http\Controllers;

use App\Models\Eloquent\Account;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function verify(Request $request): JsonResponse
    {
        $account = Account::find($request->route('id'));
        if ($account === null) {
            throw new AuthorizationException;
        }
        if ($account->hasVerifiedEmail()) {
            abort(code: 410);
        }
        if (! hash_equals(sha1($account->getEmailForVerification()), (string) $request->route('hash'))) {
            throw new AuthorizationException;
        }

        $account->markEmailAsVerified();

        event(new Verified($account));

        return response()->json();
    }

    public function resend(Request $request): JsonResponse
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json();
    }
}
