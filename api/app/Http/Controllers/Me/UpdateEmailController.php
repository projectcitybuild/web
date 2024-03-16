<?php

namespace App\Http\Controllers\Me;

use App\Actions\Auth\SendEmailChangeVerification;
use App\Actions\Me\UpdateUserEmail;
use App\Http\Controllers\Controller;
use App\Models\Rules\EmailValidationRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateEmailController extends Controller
{
    use EmailValidationRules;

    public function store(Request $request, SendEmailChangeVerification $sendEmailChangeVerification): JsonResponse
    {
        $request->validate([
            'email' => $this->emailRules(),
        ]);
        $sendEmailChangeVerification->send(
            account: $request->user(),
            newEmailAddress: $request->get('email'),
        );

        return response()->json();
    }

    public function update(Request $request, UpdateUserEmail $updateUserEmail): RedirectResponse
    {
        $token = $request->get('token');
        if ($token === null) {
            abort(401);
        }

        $updateUserEmail->update(token: $token);

        // TODO: move this to frontend
        return response()->redirectTo(config('app.frontend_url') . '/dashboard/security/change-email?verified=1');
    }
}
