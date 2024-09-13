<?php

namespace App\Http\Controllers\Front\Account\Settings;

use App\Domains\EmailChange\UseCases\SendEmailChangeEmail;
use App\Domains\EmailChange\UseCases\UpdateAccountEmail;
use App\Http\Controllers\WebController;
use App\Http\Requests\AccountChangeEmailRequest;
use App\Models\EmailChange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class UpdateEmailController extends WebController
{
    public function show(Request $request)
    {
        return view('front.pages.account.settings.update-email');
    }

    public function store(
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

    public function update(
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
                ->route('front.account.settings.update-email')
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
            ->route('front.pages.account.settings.update-email')
            ->with(['success' => 'Your email address has been updated']);
    }
}
