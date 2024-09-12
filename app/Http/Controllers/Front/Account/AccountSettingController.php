<?php

namespace App\Http\Controllers\Front\Account;

use App\Domains\EmailChange\UseCases\SendEmailChangeEmail;
use App\Domains\EmailChange\UseCases\UpdateAccountEmail;
use App\Http\Controllers\WebController;
use App\Http\Requests\AccountChangeEmailRequest;
use App\Http\Requests\AccountChangePasswordRequest;
use App\Http\Requests\AccountChangeUsernameRequest;
use App\Models\EmailChange;
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

    public function sendEmailChangeEmail(
        AccountChangeEmailRequest $request,
        SendEmailChangeEmail $sendVerificationEmail,
    ): RedirectResponse {
        $input = $request->validated();

        $account = $request->user();

        $sendVerificationEmail->execute(
            accountId: $account->getKey(),
            newEmailAddress: $input['email'],
        );

        return redirect()
            ->back()
            ->with(['success' => 'A verification email has been sent to your new email address with a link to complete the email address update']);
    }

    public function changeEmail(
        Request $request,
        UpdateAccountEmail $updateAccountEmail
    ) {
        $token = $request->get('token');
        $account = $request->user();

        $changeRequest = EmailChange::whereToken($token)
            ->whereActive()
            ->first();

        if ($changeRequest === null) {
            return redirect()
                ->route('front.account.settings')
                ->withErrors(['error' => 'Invalid or expired link']);
        }
        if ($changeRequest->account->getKey() !== $account->getKey()) {
            abort(403);
        }
        $updateAccountEmail->execute(
            account: $changeRequest->account,
            emailChangeRequest: $changeRequest,
            oldEmail: $account->email,
        );

        return redirect()
            ->route('front.account.settings')
            ->with(['success' => 'Your email address has been updated']);
    }

    public function changePassword(AccountChangePasswordRequest $request)
    {
        $input = $request->validated();

        $account = $request->user();
        $account->updatePassword($input['new_password']);

        return redirect()
            ->back()
            ->with(['success' => 'Password successfully updated']);
    }

    public function changeUsername(AccountChangeUsernameRequest $request)
    {
        $input = $request->validated();

        $account = $request->user();
        $account->username = $input['username'];
        $account->save();

        return redirect()
            ->back()
            ->with(['success', 'Username successfully updated']);
    }
}
