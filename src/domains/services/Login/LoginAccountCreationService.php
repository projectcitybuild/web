<?php
namespace Domains\Services\Login;

use Entities\Accounts\Repositories\AccountRepository;
use Entities\Accounts\Repositories\AccountLinkRepository;
use Entities\Accounts\Models\Account;
use Domains\Library\OAuth\OAuthUser;
use Illuminate\Log\Logger;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;


class LoginAccountCreationService
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var AccountLinkRepository
     */
    private $accountLinkRepository;

    /**
     * @var Logger
     */
    private $log;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Account
     */
    private $account;


    public function __construct(AccountRepository $accountRepository,
                                AccountLinkRepository $accountLinkRepository,
                                Logger $logger,
                                Connection $connection)
    {
        $this->accountRepository = $accountRepository;
        $this->accountLinkRepository = $accountLinkRepository;
        $this->log = $logger;
        $this->connection = $connection;
    }


    public function getAccount() : Account
    {
        return $this->account;
    }

    public function hasAccountLink(OAuthUser $providerAccount) : bool
    {
        $existingLink = $this->accountLinkRepository->getByProviderAccount($providerAccount->getProviderName(), 
                                                                           $providerAccount->getId());
        if ($existingLink !== null) {
            $this->account = $existingLink->account;
            return true;
        }
            
        // if an account link doesn't exist, we need to
        // check that the email is not already in use
        // by a different account, because PCB and Discourse
        // accounts must have a unique email
        $existingAccount = $this->accountRepository->getByEmail($providerAccount->getEmail());
        if ($existingAccount !== null) {
            $this->log->debug('Account with email ('.$providerAccount->getEmail().') already exists; showing error to user');
            
            throw new SocialEmailInUseException();
        }

        $this->account = $existingAccount;

        return false;
    }

    public function generateSignedRegisterUrl(OAuthUser $providerAccount)
    {
        // otherwise send them to the register confirmation
        // view using their provider account data
        $url = URL::temporarySignedRoute('front.login.social-register',
                                         now()->addMinutes(10),
                                         $providerAccount->toArray());

        $this->log->debug('Generating OAuth register URL: '.$url);

        return [
            'social' => $providerAccount->toArray(),
            'url'    => $url,
        ];
    }

    public function createAccountWithLink(string $providerEmail, string $providerId, string $providerName)
    {
        $accountLink = $this->accountLinkRepository->getByProviderAccount($providerName, $providerId);
        if ($accountLink !== null) {
            throw new \Exception('Attempting to create PCB account via OAuth, but OAuth account already exists');
        }

        $account = null;
        $this->connection->beginTransaction();
        try {
            // create a PCB account for the user
            $account = $this->accountRepository->create($providerEmail,
                                                        Hash::make(time()),
                                                        null,
                                                        now());

            // and a link to their OAuth provider account
            $this->accountLinkRepository->create($account->getKey(),
                                                 $providerName,
                                                 $providerId,
                                                 $providerEmail);

            $this->connection->commit();

        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }


}