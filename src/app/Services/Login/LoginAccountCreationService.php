<?php
namespace App\Services\Login;

use App\Entities\Accounts\Repositories\AccountRepository;
use App\Entities\Accounts\Repositories\AccountLinkRepository;
use App\Entities\Accounts\Models\Account;
use Domains\Library\OAuth\Entities\OAuthUser;
use App\Services\Login\Exceptions\SocialEmailInUseException;
use Illuminate\Log\Logger;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use App\Services\Login\Exceptions\SocialAccountAlreadyInUseException;


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


    public function __construct(
        AccountRepository $accountRepository,
        AccountLinkRepository $accountLinkRepository,
        Logger $logger,
        Connection $connection
    ) {
        $this->accountRepository = $accountRepository;
        $this->accountLinkRepository = $accountLinkRepository;
        $this->log = $logger;
        $this->connection = $connection;
    }

    public function hasAccountLink(OAuthUser $providerAccount) : bool
    {
        $existingLink = $this->accountLinkRepository->getByProviderAccount(
            $providerAccount->getProviderName(), 
            $providerAccount->getId()
        );
        if ($existingLink !== null) {
            return true;
        }
        return false;
    }

    public function getLinkedAccount(OAuthUser $providerAccount) : ?Account
    {
        $existingLink = $this->accountLinkRepository->getByProviderAccount(
            $providerAccount->getProviderName(), 
            $providerAccount->getId()
        );
        if ($existingLink !== null) {
            return $existingLink->account;
        }
        return null;
    }

    public function generateSignedRegisterUrl(OAuthUser $providerAccount)
    {
        // otherwise send them to the register confirmation
        // view using their provider account data
        $url = URL::temporarySignedRoute(
            'front.login.social-register',
            now()->addMinutes(10),
            $providerAccount->toArray()
        );

        $this->log->debug('Generating OAuth register URL: '.$url);

        return [
            'social' => $providerAccount->toArray(),
            'url'    => $url,
        ];
    }

    public function createAccountWithLink(string $providerEmail, string $providerId, string $providerName) : ?Account
    {
        $accountLink = $this->accountLinkRepository->getByProviderAccount($providerName, $providerId);
        
        if ($accountLink !== null) 
        {
            // if somehow the account link already exists, attempt to recover by
            // proceeding with that account if appropriate to do so
            $associatedAccount = $accountLink->account;

            if ($associatedAccount !== null && $associatedAccount->email === $providerEmail)
            {
                return $associatedAccount;
            }
            throw new SocialAccountAlreadyInUseException('Attempting to create PCB account via OAuth, but OAuth account already in use');
        }

        $account = null;
        $this->connection->beginTransaction();
        try 
        {
            // create a PCB account for the user
            $account = $this->accountRepository->create(
                $providerEmail,
                Hash::make(time()),
                null,
                now()
            );

            // and a link to their OAuth provider account
            $this->accountLinkRepository->create(
                $account->getKey(),
                $providerName,
                $providerId,
                $providerEmail
            );

            $this->connection->commit();
        } 
        catch (\Exception $e) 
        {
            $this->connection->rollBack();
            throw $e;
        }

        return $account;
    }

}