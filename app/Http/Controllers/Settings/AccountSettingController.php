<?php

namespace App\Http\Controllers\Settings;

use App\Http\Requests\AccountChangeEmailRequest;
use App\Http\Requests\AccountChangePasswordRequest;
use App\Http\Requests\AccountChangeUsernameRequest;
use App\Http\WebController;
use Domain\EmailChange\Exceptions\TokenNotFoundException;
use Domain\EmailChange\UseCases\SendVerificationEmail;
use Domain\EmailChange\UseCases\UpdateAccountEmail;
use Domain\EmailChange\UseCases\VerifyEmail;
use Entities\Models\Eloquent\AccountEmailChange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AccountSettingController extends WebController
{
    public function show(Request $request)
    {
        $user = $request->user();

        return view('front.pages.account.account-settings')
            ->with(compact('user'));
    }

    public function sendVerificationEmail(
        AccountChangeEmailRequest $request,
        SendVerificationEmail     $sendVerificationEmail,
    ): RedirectResponse {
        $input = $request->validated();
        $account = $request->user();

        $sendVerificationEmail->execute(
            accountId: $account->getKey(),
            oldEmailAddress: $account->email,
            newEmailAddress: $input['email'],
        );

        return redirect()->back()->with([
            'success' => '
                A verification email has been sent to your new and current email address.
                Please click the link in both emails to complete the process
            ',
        ]);
    }

    /**
     * Either shows information about the current stage in the email change process,
     * or completes the email change process if the user has just finished verifying
     * they own both email addresses (old and new).
     */
    public function showConfirmForm(
        Request            $request,
        VerifyEmail        $verifyEmail,
        UpdateAccountEmail $updateAccountEmail
    ) {
        try {
            return $verifyEmail->execute(
                token: $request->get('token'),
                email: $request->get('email'),
                onHalfComplete: fn (AccountEmailChange $changeRequest) => view(
                    view: 'front.pages.account.account-settings-email-confirm',
                    data: ['changeRequest' => $changeRequest]
                ),
                onBothComplete: function (AccountEmailChange $changeRequest) use ($updateAccountEmail) {
                    $updateAccountEmail->execute(
                        account: $changeRequest->account,
                        emailChangeRequest: $changeRequest,
                    );

                    return view('front.pages.account.account-settings-email-complete');
                },
            );
        } catch (TokenNotFoundException) {
            // Token has expired or the email change process has already been completed
            abort(code: 410);
        }
    }

    public function changePassword(AccountChangePasswordRequest $request)
    {
        $input = $request->validated();

        $account = $request->user();
        $account->updatePassword($input['new_password']);

        return redirect()
            ->route('front.account.security')
            ->with(['success_password' => 'Password successfully updated']);
    }

    public function changeUsername(AccountChangeUsernameRequest $request)
    {
        $input = $request->validated();

        $account = $request->user();
        $account->username = $input['username'];
        $account->save();

        return redirect()
            ->route('front.account.settings')
            ->with(['success_username', 'Username successfully updated']);
    }
}
