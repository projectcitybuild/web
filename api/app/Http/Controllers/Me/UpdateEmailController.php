<?php

namespace App\Http\Controllers\Me;

use App\Actions\Auth\SendEmailChangeVerification;
use App\Actions\Me\UpdateUserEmail;
use App\Http\Controllers\Controller;
use App\Models\Rules\EmailValidationRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateEmailController extends Controller
{
    use EmailValidationRules;

    /**
     * @throws ValidationException
     */
    public function store(Request $request, SendEmailChangeVerification $sendEmailChangeVerification): JsonResponse
    {
        Validator::make($request->input(), [
            'email' => $this->emailRules(),
        ])->validate();

        $sendEmailChangeVerification->send(
            account: $request->user(),
            newEmailAddress: $request->get('email'),
        );

        return response()->json();
    }

    public function update(Request $request, UpdateUserEmail $updateUserEmail)
    {
        $token = $request->get('token');
        if ($token === null) {
            return response()->json([], 400);
        }

        $updateUserEmail->update(token: $token);

        // TODO: move this to frontend
        return response()->redirectTo(config('app.frontend_url') . '/dashboard/security/change-email?verified=1');
    }
}
