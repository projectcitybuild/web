<?php

namespace App\Http\Controllers\Account;

use App\Actions\Auth\SendEmailChangeVerification;
use App\Http\Controllers\Controller;
use App\Rules\EmailValidationRules;
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

        $account = $request->user();
        $sendEmailChangeVerification->send(
            account: $account,
            newEmailAddress: $request->get('email'),
        );

        return response()->json();
    }
}
