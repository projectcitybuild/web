<?php

namespace App\Http\Controllers;

use App\Entities\Accounts\Repositories\AccountEmailChangeRepository;
use App\Http\Requests\AccountChangeEmailRequest;
use App\Http\Requests\AccountChangePasswordRequest;
use App\Http\Actions\AccountSettings\UpdateAccountPassword;
use App\Http\Actions\AccountSettings\SendEmailForAccountEmailChange;
use App\Http\WebController;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Http\Actions\AccountSettings\UpdateAccountEmail;

final class AccountSettingController extends WebController
{
    public function showView()
    {
        return view('front.pages.account.account-settings');
    }

    public function sendVerificationEmail(AccountChangeEmailRequest $request, SendEmailForAccountEmailChange $sendEmailForAccountEmailChange)
    {
        $input = $request->validated();

        $sendEmailForAccountEmailChange->execute(
            $request->user(),
            $input['email']
        );

        return redirect()->back()->with([
            'success' => 'A verification email has been sent to both your new and current email address. Please click the link in both to complete the process'
        ]);
    }

    /**
     * Either shows information about the current stage in the email change process,
     * or completes the email change process if the user has just finished verifying
     * they own both email addresses (current and new)
     *
     * @param Request $request
     * @return View
     */
    public function showConfirmForm(Request $request, AccountEmailChangeRepository $emailChangeRepository, UpdateAccountEmail $updateAccountEmail)
    {
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

        if (!$areBothAddressesVerified) {
            $changeRequest->save();

            return view('front.pages.account.account-settings-email-confirm', [
                'changeRequest' => $changeRequest,
            ]);

        } else {
            // Otherwise, change their email address and complete the process
            $updateAccountEmail->execute(
                $changeRequest->account,
                $changeRequest
            );

            return view('front.pages.account.account-settings-email-complete');
        }
    }

    public function changePassword(AccountChangePasswordRequest $request, UpdateAccountPassword $updatePassword)
    {
        $input = $request->validated();

        $updatePassword->execute(
            $request->user(), 
            $input['new_password']
        );

        return redirect()
            ->route('front.account.settings')
            ->with(['success_password' => 'Password successfully updated']);
    }
}
