<?php
namespace App\Modules\Accounts\Services;

use App\Modules\Accounts\Repositories\AccountLinkRepository;
use App\Modules\Accounts\Repositories\AccountRepository;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Carbon\Carbon;
use App\Modules\Accounts\Models\Account;
use Hash;

class AccountLinkService {

    /**
     * @var AccountLinkRepository
     */
    private $accountLinkRepository;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    public function __construct(
        AccountLinkRepository $accountLinkRepository,
        AccountRepository $accountRepository
    ) {
        $this->accountLinkRepository = $accountLinkRepository;
        $this->accountRepository = $accountRepository;
    }

    public function getOrCreateAccount(string $providerName, ProviderUser $providerUser) : Account {
        $account = $this->accountRepository->getByEmail($providerUser->getEmail());
        if($account === null) {
            $account = $this->accountRepository->create(
                $providerUser->getEmail(),
                Hash::make(time()),
                null,
                Carbon::now()
            );
        }
        
        // get or create a social account link
        $accountLink = $this->accountLinkRepository->getByProvider($providerName, $providerUser->getId());
        if($accountLink === null) {
            $accountLink = $this->accountLinkRepository->create(
                $providerName, 
                $providerUser->getId(), 
                $account->getKey()
            );
        }

        return $account;
    }

}