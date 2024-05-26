<?php

namespace App\Http\Controllers\Auth;

use App\Domains\Registration\Actions\ResendVerificationLink;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResendVerificationEmailController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function __invoke(
        Request                $request,
        ResendVerificationLink $resendVerificationEmail,
    ): JsonResponse
    {
        $resendVerificationEmail->call(account: $request->user());

        return response()->json();
    }
}
