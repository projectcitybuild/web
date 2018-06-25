<?php

namespace Front\Controllers;

use Illuminate\Support\Facades\View;
use App\Modules\Accounts\Models\Account;
use App\Modules\Accounts\Repositories\AccountRepository;
use App\Modules\Accounts\Notifications\AccountEmailChangeVerifyNotification;
use Front\Requests\ChangeEmailRequest;
use Front\Requests\SaveNewEmailRequest;
use Illuminate\Support\Facades\Auth;

class AccountSettingController extends WebController {

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    public function __construct(AccountRepository $accountRepository) {
        $this->accountRepository = $accountRepository;
    }


    public function showView() {
        return view('account-settings');
    }

    public function sendVerificationEmail(ChangeEmailRequest $request) {
        $input = $request->validated();
        $newEmail = $input['email'];
        
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

    public function confirmEmailChange(SaveNewEmailRequest $request) {
        // TODO: verify payloads to prevent hidden field tampering...

        $input = $request->validated();
        $oldEmail = $input['old_email'];
        $newEmail = $input['new_email'];

        $account = $this->accountRepository->getByEmail($oldEmail);

        if ($account === null) {
            throw new \Exception('Email address could not be found');
        }

        // TODO: push to Discourse

        $account->email = $newEmail;
        $account->save();

    }

}
