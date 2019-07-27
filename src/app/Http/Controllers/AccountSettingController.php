<?php

namespace App\Http\Controllers;

use App\Entities\Accounts\Repositories\AccountEmailChangeRepository;
use App\Http\Requests\AccountChangeEmailRequest;
use App\Http\Requests\AccountChangePasswordRequest;
use App\Http\Actions\AccountSettings\UpdateAccountPassword;
use App\Http\Actions\AccountSettings\SendEmailForAccountEmailChange;
use App\Http\WebController;
use App\Library\Discourse\Api\DiscourseAdminApi;
use App\Library\Discourse\Entities\DiscoursePayload;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

final class AccountSettingController extends WebController
{
    /**
     * @var DiscourseAdminApi
     */
    private $discourseApi;

    /**
     * @var AccountEmailChangeRepository
     */
    private $emailChangeRepository;


    public function __construct(DiscourseAdminApi $discourseApi, AccountEmailChangeRepository $emailChangeRepository) {
        $this->discourseApi = $discourseApi;
        $this->emailChangeRepository = $emailChangeRepository;
    }

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
