<?php
namespace App\Library\Socialite;

use Laravel\Socialite\Facades\Socialite;
use App\Library\Socialite\Exceptions\UnsupportedProviderException;

class SocialiteService {

    public const FACEBOOK   = 'facebook';
    public const TWITTER    = 'twitter';
    public const GOOGLE     = 'google';

    /**
     * @var string
     */
    private $providerName;


    public function __construct(Socialite $socialFacade) {
        $this->socialFacade = $socialFacade;
    }

    public function setProvider(string $providerName) : SocialiteService {
        $this->providerName = $providerName;
        return $this;
    }

    private function getDriver() {
        if($this->providerName === null) {
            throw new \Exception('Provider not set');
        }
        return Socialite::driver($this->providerName);
    }

    public function redirectToProviderLogin() {
        return $this->getDriver()->redirect();
    }
    
    /**
     * Retrieves a user from the OAuth provider response and
     * returns a AccountSocialModel
     *
     * @return SocialiteData
     */
    public function getProviderResponse() : SocialiteData {
        $user = $this->getDriver()->user();

        if($user->getEmail() === null) {
            throw new UnsupportedProviderException('Provider ['.$this->providerName.'] does not give read access to an email address');
        }
        if($user->getId() === null) {
            throw new UnsupportedProviderException('Provider ['.$this->providerName.'] does not provide a unique identifier');
        }

        return new SocialiteData($this->providerName,
                                 $user->getEmail(),
                                 $user->name ?: "Hidden Name",
                                 $user->getId());
    }

}
