<?php

namespace Front\Controllers;

use App\Modules\Accounts\Repositories\AccountRepository;
use App\Modules\Accounts\Repositories\AccountPasswordResetRepository;
use App\Modules\Accounts\Notifications\AccountPasswordResetNotification;
use App\Modules\Accounts\Notifications\AccountPasswordResetCompleteNotification;
use Front\Requests\ResetPasswordRequest;
use Front\Requests\SendPasswordEmailRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Connection;
use Hash;
use App\Support\Helpers\TokenHelpers;

class PasswordRecoveryController extends WebController {

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var AccountPasswordResetRepository
     */
    private $passwordResetRepository;

    /**
     * @var Connection
     */
    private $connection;


    public function __construct(
        AccountRepository $accountRepository, 
        AccountPasswordResetRepository $passwordResetRepository,
        Connection $connection
    ) {
        $this->accountRepository = $accountRepository;
        $this->passwordResetRepository = $passwordResetRepository;
        $this->connection = $connection;
    }

    public function showEmailForm() {
        return view('front.pages.password-reset.password-reset');
    }

    public function sendVerificationEmail(SendPasswordEmailRequest $request) {
        $input = $request->validated();
        $email = $input['email'];

        $token = TokenHelpers::generateToken();
        $passwordReset = $this->passwordResetRepository->updateOrCreateByEmail($email, $token);

        $account = $request->getAccount();
        $account->notify(new AccountPasswordResetNotification($passwordReset));

        return redirect()
            ->back()
            ->with(['success' => $email]);
    }

    public function showResetForm(Request $request) {
        // make sure a token was provided
        $token = $request->get('token');
        if($token === null) {
            abort(401, "No token provided");
        }

        $passwordReset = $this->passwordResetRepository->getByToken($token);
        if($passwordReset === null) {
            abort(401, "Token invalid or has expired");
        }

        return view('front.pages.password-reset.password-reset-form', [
            'passwordToken' => $request->get('token')
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request) {
        $input = $request->validated();
        $token = $input['password_token'];

        $passwordReset = $this->passwordResetRepository->getByToken($token);
        if($passwordReset === null) {
            return redirect()
                ->route('front.password-reset')
                ->withErrors('error', 'Password token not found');
        }

        $account = $this->accountRepository->getByEmail($passwordReset->email);
        if($account === null) {
            return redirect()
                ->route('front.password-reset')
                ->withErrors('error', 'Account not found');
        }

        $this->connection->beginTransaction();
        try {
            $account->password = Hash::make($request->get('password'));
            $account->save();

            $passwordReset->delete();

            $this->connection->commit();

        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

        $account->notify(new AccountPasswordResetCompleteNotification);

        return view('front.pages.password-reset.password-reset-success');
    }



}
