<?php

namespace Front\Controllers;

use App\Modules\Accounts\Models\Account;
use App\Modules\Accounts\Repositories\AccountRepository;
use App\Modules\Accounts\Notifications\AccountEmailChangeVerifyNotification;
use Front\Requests\AccountChangeEmailRequest;
use Front\Requests\AccountSaveNewEmailRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Front\Requests\AccountChangePasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Modules\Discourse\Services\Api\DiscourseAdminApi;
use App\Modules\Discourse\Services\Authentication\DiscoursePayload;
use App\Modules\Accounts\Repositories\AccountEmailChangeRepository;
use App\Core\Helpers\TokenHelpers;
use Illuminate\Database\Connection;

class AccountSettingController extends WebController {

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

    /**
     * @var Connection
     */
    private $connection;


    public function __construct(AccountRepository $accountRepository, 
                                DiscourseAdminApi $discourseApi,
                                AccountEmailChangeRepository $emailChangeRepository,
                                Connection $connection) 
    {
        $this->accountRepository = $accountRepository;
        $this->discourseApi = $discourseApi;
        $this->emailChangeRepository = $emailChangeRepository;
        $this->connection = $connection;
    }


    public function showView() {
        return view('account-settings');
    }

    public function sendVerificationEmail(AccountChangeEmailRequest $request) {
        $input = $request->validated();
        $newEmail = $input['email'];

        $account = $request->user();
        if ($account === null) {
            throw new \Exception('Account is null');
        }

        $token = TokenHelpers::generateToken();
        $changeRequest = $this->emailChangeRepository->create($account->getKey(), 
                                                              $token, 
                                                              $account->email, 
                                                              $newEmail);
        
        // send email to current email address
        $mail = new AccountEmailChangeVerifyNotification($account->email, $changeRequest->getCurrentEmailUrl(20));
        $mail->isOldEmailAddress = true;
        $account->notify($mail);

        // send email to new email address
        $mail = new AccountEmailChangeVerifyNotification($newEmail, $changeRequest->getNewEmailUrl(20));
        $account->notify($mail);

        return redirect()
            ->back()
            ->with(['success' => 'A verification email has been sent to both your new and current email address. Please click the link in both to complete the process']);
    }

    public function showConfirmForm(Request $request) {
        $token = $request->get('token');
        if (empty($token)) {
            abort(404);
        }

        $email = $request->get('email');
        if (empty($email)) {
            throw new \Exception('No email address supplied during email change confirmation');
        }

        $changeRequest = $this->emailChangeRepository->getByToken($token);
        if ($changeRequest === null) {
            throw new \Exception('Email change request not found for provided token...');
        }

        if ($email === $changeRequest->email_previous) {
            $changeRequest->is_previous_confirmed = true;
        } 
        elseif ($email === $changeRequest->email_new) {
            $changeRequest->is_new_confirmed = true;
        } 
        else {
            throw new \Exception('Provided email does not match any email in the stored change request');
        }

        if ($changeRequest->is_previous_confirmed === true && $changeRequest->is_new_confirmed === true) {

            $this->connection->beginTransaction();

            try {
                $account = $changeRequest->account;
                if ($account === null) {
                    throw new \Exception('No account belongs to this email change request');
                }
    
                if (empty($changeRequest->email_new)) {
                    throw new \Exception('Missing `new email` address in email change request');
                }
    
                $payload = (new DiscoursePayload)
                    ->setPcbId(1)
                    ->setEmail($changeRequest->email_new)
                    ->build();
    
                $this->discourseApi->requestSSOSync($payload);

                $account->email = $newEmail;
                $account->save();
    
                $changeRequest->delete();

                $this->connection->commit();

                return view('account-settings-email-confirm');
            
            } catch(\Exception $e) {
                $this->connection->rollBack();
                throw $e;
            }

        } else {
            $changeRequest->save();

            return view('account-settings-email-confirm', [
                'changeRequest' => $changeRequest,
            ]);
        }
    }

    public function changePassword(AccountChangePasswordRequest $request) {
        $input = $request->validated();

        $password = $input['new_password'];

        $account = $request->user();
        $account->password = Hash::make($password);
        $account->save();

        return redirect()
            ->route('front.account.settings')
            ->with(['success_password' => 'Password successfully updated']);
    }

}
