<?php

namespace App\Http\Controllers;

use App\Models\Eloquent\Account;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function verify(Request $request, String $id): JsonResponse
    {
        $account = Account::find($id);

        if ($account === null) {
            throw new AuthorizationException;
        }
        if ($account->hasVerifiedEmail()) {
            return response()->json(data: null, status: 204);
        }
        if (! hash_equals(sha1($account->getEmailForVerification()), (string) $request->route('hash'))) {
            throw new AuthorizationException;
        }

        $account->markEmailAsVerified();

        event(new Verified($account));

        return response()->json();
    }

    public function resend(Request $request, String $id): JsonResponse
    {
        $account = Account::find($id);

        if ($account === null) {
            throw new AuthorizationException;
        }
        if ($account->hasVerifiedEmail()) {
            return response()->json(data: null, status: 204);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json();
    }
}
