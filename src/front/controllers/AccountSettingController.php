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

class AccountSettingController extends WebController {

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var DiscourseAdminApi
     */
    private $discourseApi;

    public function __construct(AccountRepository $accountRepository, 
                                DiscourseAdminApi $discourseApi) {

        $this->accountRepository = $accountRepository;
        $this->discourseApi = $discourseApi;
    }


    public function showView() {
        return view('account-settings');
    }

    public function sendVerificationEmail(AccountChangeEmailRequest $request) {
        $input = $request->validated();
        $newEmail = $input['email'];

        $account = $request->user();

        // $payload = (new DiscoursePayload)
        //     ->setPcbId(1)
        //     ->setEmail($newEmail)
        //     ->build();

        // try {
        //     $response = $this->discourseApi->requestSSOSync($payload);
        //     dd($response);
            
        // } catch(\Exception $e) {
        //     dd($e);
        // }

        $account = $request->user();
        $account->notify(new AccountEmailChangeVerifyNotification($newEmail, $account));

        return redirect()
            ->back()
            ->with(['success' => 'A verification email has been sent to your new email address. Please follow the instructions to complete the process']);
    }

    public function showConfirmForm(Request $request) {
        $oldEmail = $request->get('old_email');
        $newEmail = $request->get('new_email');

        if (empty($oldEmail)) {
            throw new \Exception('oldEmail is empty or missing');
        }
        if (empty($newEmail)) {
            throw new \Exception('newEmail is empty or missing');
        }

        return view('account-settings-email-confirm', [
            'oldEmail' => $oldEmail,
            'newEmail' => $newEmail,
        ]);
    }

    public function confirmEmailChange(AccountSaveNewEmailRequest $request) {
        // TODO: verify payloads to prevent hidden field tampering...

        $input = $request->validated();
        $oldEmail = $input['old_email'];
        $newEmail = $input['new_email'];
        $password = $input['password'];

        $account = $this->accountRepository->getByEmail($oldEmail);

        if ($account === null) {
            throw new \Exception('Email address could not be found');
        }

        // TODO: push to Discourse

        $account->email = $newEmail;
        $account->save();

        // Auth::logoutOtherDevices($password);
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
