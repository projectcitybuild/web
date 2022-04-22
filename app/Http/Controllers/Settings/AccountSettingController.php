<?php

namespace App\Http\Controllers\Settings;

use App\Http\Actions\AccountSettings\UpdateAccountPassword;
use App\Http\Actions\AccountSettings\UpdateAccountUsername;
use App\Http\Requests\AccountChangeEmailRequest;
use App\Http\Requests\AccountChangePasswordRequest;
use App\Http\Requests\AccountChangeUsernameRequest;
use App\Http\WebController;
use Domain\EmailChange\SendEmailForAccountEmailChange;
use Domain\EmailChange\UpdateAccountEmail;
use Entities\Repositories\AccountEmailChangeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

final class AccountSettingController extends WebController
{
    public function showView(Request $request)
    {
        $user = $request->user();

        return view('v2.front.pages.account.account-settings')->with(compact('user'));
    }

    public function sendVerificationEmail(
        AccountChangeEmailRequest $request,
        SendEmailForAccountEmailChange $sendEmailForAccountEmailChange
    ) {
        $input = $request->validated();

        $sendEmailForAccountEmailChange->execute(
            $request->user(),
            $input['email']
        );

        return redirect()->back()->with([
            'success' => 'A verification email has been sent to both your new and current email address. Please click the link in both to complete the process',
        ]);
    }

    /**
     * Either shows information about the current stage in the email change process,
     * or completes the email change process if the user has just finished verifying
     * they own both email addresses (current and new).
     *
     *
     * @return View
     */
    public function showConfirmForm(
        Request $request,
        AccountEmailChangeRepository $emailChangeRepository,
        UpdateAccountEmail $updateAccountEmail
    ) {
        $token = $request->get('token');
        $email = $request->get('email');

        if (empty($token) || empty($email)) {
            // If the email or token field is missing, the user has highly likely
            // tampered with the URL
            throw new \Exception('Email address and/or token missing');
        }

        $changeRequest = $emailChangeRepository->getByToken($token);
        if ($changeRequest === null) {
            // Token has expired or the email change process has already been completed
            abort(410);
        }

        switch ($email) {
        case $changeRequest->email_previous:
            $changeRequest->is_previous_confirmed = true;
            break;

        case $changeRequest->email_new:
            $changeRequest->is_new_confirmed = true;
            break;

        default:
            // If the supplied email matches neither the old nor the new email address in
            // the stored email change request, the request cannot be performed
            throw new \Exception('Provided email address does not match the current or new email address');
        }

        $areBothAddressesVerified = $changeRequest->is_previous_confirmed && $changeRequest->is_new_confirmed;

        if (! $areBothAddressesVerified) {
            $changeRequest->save();

            return view('v2.front.pages.account.account-settings-email-confirm', [
                'changeRequest' => $changeRequest,
            ]);
        }

        $updateAccountEmail->execute(
            $changeRequest->account,
            $changeRequest
        );

        return view('v2.front.pages.account.account-settings-email-complete');
    }

    public function changePassword(
        AccountChangePasswordRequest $request,
        UpdateAccountPassword $updatePassword
    ) {
        $input = $request->validated();

        $updatePassword->execute(
            $request->user(),
            $input['new_password']
        );

        return redirect()
            ->route('front.account.security')
            ->with(['success_password' => 'Password successfully updated']);
    }

    public function changeUsername(
        AccountChangeUsernameRequest $request,
        UpdateAccountUsername $updateUsername
    ) {
        $input = $request->validated();

        $updateUsername->execute(
            $request->user(),
            $input['username']
        );

        return redirect()
            ->route('front.account.settings')
            ->with(['success_username', 'Username successfully updated']);
    }
}
