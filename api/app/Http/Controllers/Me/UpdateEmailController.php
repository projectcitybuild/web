<?php

namespace App\Http\Controllers\Me;

use App\Domains\Accounts\Data\Rules\EmailValidationRules;
use App\Domains\AccountSecurity\Actions\SendEmailChangeVerification;
use App\Domains\EditAccount\Actions\UpdateUserEmail;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateEmailController extends Controller
{
    use EmailValidationRules;

    public function store(
        Request $request,
        SendEmailChangeVerification $sendEmailChangeVerification,
    ): JsonResponse
    {
        $request->validate([
            'email' => $this->emailRules(),
        ]);
        $sendEmailChangeVerification->call(
            account: $request->user(),
            newEmailAddress: $request->get('email'),
        );

        return response()->json();
    }

    public function update(
        Request $request,
        UpdateUserEmail $updateUserEmail,
    ): RedirectResponse
    {
        $token = $request->get('token');
        if ($token === null) {
            abort(code: 401);
        }

        $updateUserEmail->call(token: $token);

        // TODO: move this to frontend
        return response()->redirectTo(config('app.frontend_url') . '/dashboard/security/change-email?verified=1');
    }
}
