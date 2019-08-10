<?php

namespace App\Http\Actions\AccountRegistration;

use Illuminate\Support\Facades\Hash;
use App\Entities\Accounts\Notifications\AccountActivationNotification;
use App\Entities\Accounts\Repositories\UnactivatedAccountRepository;

final class SendRegisterVerificationEmail
{
    private $unactivatedAccountRepository;
    
    public function __construct(UnactivatedAccountRepository $unactivatedAccountRepository) 
    {
        $this->unactivatedAccountRepository = $unactivatedAccountRepository;
    }

    public function execute(string $email, string $username, string $password)
    {
        $hashedPassword = Hash::make($password);

        $unactivatedAccount = $this->unactivatedAccountRepository->create($email, $username, $hashedPassword);
        $unactivatedAccount->notify(new AccountActivationNotification($unactivatedAccount));
    }
}