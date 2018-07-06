<?php
namespace App\Modules\Accounts\Services;

use App\Modules\Accounts\Repositories\AccountLinkRepository;
use App\Modules\Accounts\Repositories\AccountRepository;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Carbon\Carbon;
use App\Modules\Accounts\Models\Account;
use Hash;

class AccountSocialLinkService {

    /**
     * @var AccountLinkRepository
     */
    private $accountLinkRepository;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    public function __construct(AccountLinkRepository $accountLinkRepository,
                                AccountRepository $accountRepository) 
    {
        $this->accountLinkRepository = $accountLinkRepository;
        $this->accountRepository = $accountRepository;
    }

    public function createAccount(string $providerName, string $email, string $id) : Account {
        $account = $this->accountRepository->create(
            $email,
            Hash::make(time()),
            null,
            Carbon::now()
        );
        
        // get or create a social account link
        $accountLink = $this->accountLinkRepository->getByProvider($providerName, $id);
        if($accountLink === null) {
            $accountLink = $this->accountLinkRepository->create(
                $providerName, 
                $id,
                $account->getKey()
            );
        }

        return $account;
    }

}