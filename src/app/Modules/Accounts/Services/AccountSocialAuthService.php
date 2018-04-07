<?php
namespace App\Modules\Accounts\Services;

use App\Modules\Accounts\Repositories\AccountRepository;
use App\Modules\Accounts\Execeptions\UnsupportedAuthProviderException;
use Laravel\Socialite\Facades\Socialite;

class AccountSocialAuthService {

    /**
     * @var string
     */
    private $providerName;


    public function __construct(Socialite $socialFacade) {
        $this->socialFacade = $socialFacade;
    }

    public function setProvider(string $providerName) : AccountSocialAuthService {
        $this->providerName = $providerName;
        return $this;
    }

    private function getDriver() {
        if($this->providerName === null) {
            throw new \Exception('Provider not set');
        }
        return Socialite::driver($this->providerName);
    }

    public function getProviderUrl() {
        return $this->getDriver()->redirect();
    }
    
    /**
     * Retrieves a user from the OAuth provider response and
     * returns a AccountSocialModel
     *
     * @return AccountSocialModel
     */
    public function handleProviderResponse() : AccountSocialModel {
        $user = $this->getDriver()->user();

        if($user->getEmail() === null) {
            throw new UnsupportedAuthProviderException('Provider ['.$this->providerName.'] does not give read access to an email address');
        }
        if($user->getId() === null) {
            throw new UnsupportedAuthProviderException('Provider ['.$this->providerName.'] does not provide a unique identifier');
        }

        return new AccountSocialModel(
            $this->providerName,
            $user->getEmail(),
            $user->name ?: "Hidden Name",
            $user->getId()
        );
    }

}