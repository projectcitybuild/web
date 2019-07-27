<?php

namespace App\Http\Controllers;

use App\Entities\Accounts\Repositories\AccountRepository;
use App\Entities\Accounts\Notifications\AccountEmailChangeVerifyNotification;
use App\Http\Requests\AccountChangeEmailRequest;
use Illuminate\Support\Facades\View;
use App\Http\Requests\AccountChangePasswordRequest;
use Illuminate\Http\Request;
use App\Library\Discourse\Api\DiscourseAdminApi;
use App\Library\Discourse\Entities\DiscoursePayload;
use App\Entities\Accounts\Repositories\AccountEmailChangeRepository;
use Illuminate\Support\Facades\Notification;
use App\Http\WebController;
use App\Helpers\TokenHelpers;
use Illuminate\Support\Facades\DB;
use App\Http\Actions\UpdateAccountPasswordAction;

final class AccountSettingController extends WebController
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var DiscourseAdminApi
     */
    private $discourseApi;

    /**
     * @var AccountEmailChangeRepository
     */
    private $emailChangeRepository;


    public function __construct(
        AccountRepository $accountRepository,
        DiscourseAdminApi $discourseApi,
        AccountEmailChangeRepository $emailChangeRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->discourseApi = $discourseApi;
        $this->emailChangeRepository = $emailChangeRepository;
    }

    public function showView()
    {
        return view('front.pages.account.account-settings');
    }

    public function sendVerificationEmail(AccountChangeEmailRequest $request)
    {
        $input = $request->validated();
        $newEmail = $input['email'];

        $account = $request->user();
        if ($account === null) {
            throw new \Exception('Account is null');
        }

        $token = TokenHelpers::generateToken();
        $changeRequest = $this->emailChangeRepository->create(
            $account->getKey(),
            $token,
            $account->email,
            $newEmail
        );
        
        // send email to current email address
        $mail = new AccountEmailChangeVerifyNotification($account->email, $changeRequest->getCurrentEmailUrl(20));
        $mail->isOldEmailAddress = true;
        $account->notify($mail);

        // send email to new email address
        $mail = new AccountEmailChangeVerifyNotification($newEmail, $changeRequest->getNewEmailUrl(20));
        Notification::route('mail', $newEmail)
            ->notify($mail);

        return redirect()
            ->back()
            ->with(['success' => 'A verification email has been sent to both your new and current email address. Please click the link in both to complete the process']);
    }

    /**
     * Shows and updates the 'email change' status view
     *
     * @param Request $request
     * @return View
     */
    public function showConfirmForm(Request $request)
    {
        $token = $request->get('token');
        $email = $request->get('email');
        
        if (empty($token) || empty($email)) {
            // if the signed url was valid and yet the email or token field is missing,
            // something has definitely gone wrong
            throw new \Exception('Email address or token missing in url parameters');
        }

        $changeRequest = $this->emailChangeRepository->getByToken($token);
        
        if ($changeRequest === null) {
            // token has either expired or the email confirmation process has already
            // been completed
            abort(404);
        }

        if ($changeRequest->account === null) {
            throw new \Exception('No account belongs to this email change request');
        }
        if (empty($changeRequest->email_new)) {
            throw new \Exception('Missing `new email` address in email change request');
        }

        if ($email === $changeRequest->email_previous) {
            $changeRequest->is_previous_confirmed = true;
        } elseif ($email === $changeRequest->email_new) {
            $changeRequest->is_new_confirmed = true;
        } else {
            // if the supplied email matches neither the
            // old or new email address in the stored
            // change request, something has gone wrong
            throw new \Exception('Provided email does not match any email in the stored change request');
        }

        // if the link in both emails has not been
        // clicked yet, save the current progres and
        // show the 'confirmation progress' view
        if ($changeRequest->is_previous_confirmed == false ||
            $changeRequest->is_new_confirmed == false) {
            $changeRequest->save();

            return view('front.pages.account.account-settings-email-confirm', [
                'changeRequest' => $changeRequest,
            ]);
        }

        // otherwise, change their email address
        // and complete the process
        DB::beginTransaction();
        try {
            $account = $changeRequest->account;

            // push the email change to Discourse
            // via the user sync route
            $payload = (new DiscoursePayload)
                ->setPcbId($account->getKey())
                ->setEmail($changeRequest->email_new);

            try {
                $this->discourseApi->requestSSOSync($payload->build());
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                // sometimes the api fails at random because
                // the 'requires_activation' key is needed in
                // the payload. As a workaround we'll send the
                // request again but with the key included
                // this time
                $payload->requiresActivation(false);
                $this->discourseApi->requestSSOSync($payload->build());
            }

            // push the email change to our own
            // local user database
            $account->email = $changeRequest->email_new;
            $account->save();

            $changeRequest->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return view('front.pages.account.account-settings-email-complete');
    }

    public function changePassword(AccountChangePasswordRequest $request, UpdateAccountPasswordAction $updatePassword)
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
