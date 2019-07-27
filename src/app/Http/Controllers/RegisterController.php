<?php

namespace App\Http\Controllers;

use App\Entities\Accounts\Repositories\AccountRepository;
use App\Entities\Accounts\Repositories\UnactivatedAccountRepository;
use App\Entities\Accounts\Notifications\AccountActivationNotification;
use App\Http\Requests\RegisterRequest;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Hash;
use App\Http\WebController;

class RegisterController extends WebController
{

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var UnactivatedAccountRepository
     */
    private $unactivatedAccountRepository;

    /**
     * @var Connection
     */
    private $connection;


    public function __construct(
        AccountRepository $accountRepository,
        UnactivatedAccountRepository $unactivatedAccountRepository,
        Connection $connection
    ) {
        $this->accountRepository = $accountRepository;
        $this->unactivatedAccountRepository = $unactivatedAccountRepository;
        $this->connection = $connection;
    }

    public function showRegisterView()
    {
        return view('front.pages.register.register');
    }

    public function register(RegisterRequest $request)
    {
        $input = $request->validated();

        $email      = $input['email'];
        $username   = $input['username'];
        $password   = $input['password'];
        $password   = Hash::make($password);

        $unactivatedAccount = $this->unactivatedAccountRepository->create($email, $username, $password);
        $unactivatedAccount->notify(new AccountActivationNotification($unactivatedAccount));

        return view('front.pages.register.register-success');
    }

    /**
     * Attempts to activate an account via token
     *
     * @param Request $request
     *
     * @return View
     * @throws \Exception
     */
    public function activate(Request $request)
    {
        $email = $request->get('email');
        if (empty($email)) {
            return view('front.pages.register.register');
        }

        $unactivatedAccount = $this->unactivatedAccountRepository->getByEmail($email);
        if ($unactivatedAccount === null) {
            // TODO: inform user that account does not exist
            abort(404);
        }

        $accountByEmail = $this->accountRepository->getByEmail($email);
        if ($accountByEmail) {
            // TODO: inform user account is already activated
            abort(410);
        }

        $this->connection->beginTransaction();
        try {
            $this->accountRepository->create(
                $unactivatedAccount->email,
                $unactivatedAccount->username,
                $unactivatedAccount->password,
                $request->ip(),
                Carbon::now()
            );

            $unactivatedAccount->delete();

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

        return view('front.pages.register.register-verify-complete');
    }
}
