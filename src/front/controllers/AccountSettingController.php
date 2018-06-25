<?php

namespace Front\Controllers;

use Illuminate\Support\Facades\View;
use App\Modules\Accounts\Models\Account;
use Front\Requests\ChangeEmailRequest;
use App\Modules\Accounts\Notifications\AccountEmailChangeVerifyNotification;
use Illuminate\Http\Request;

class AccountSettingController extends WebController {

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
            ->with(['success' => 'A verification email has been sent to your current email address. Please follow the instructions to complete the process']);
    }

    public function confirmEmailChange(Request $request) {
        
    }

}
