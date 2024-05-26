<?php

namespace App\Http\Controllers\Auth;

use App\Domains\PasswordReset\Actions\SendPasswordResetLink;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function __invoke(
        Request $request,
        SendPasswordResetLink $sendPasswordResetLink
    ): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);
        $sendPasswordResetLink->call(email: $validated['email']);

        return response()->json();
    }
}
