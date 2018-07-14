<?php
namespace App\Library\Socialite;

use Laravel\Socialite\Facades\Socialite;
use App\Library\Socialite\Exceptions\UnsupportedProviderException;
use Illuminate\Http\Request;

class SocialiteService {

    /**
     * @var Request
     */
    private $request;


    public function __construct(Request $request) {
        $this->request = $request;
    }
    

    private function getDriver(string $providerName) {
        return Socialite::driver($providerName);
    }

    private function setRedirectUrl(string $providerName, string $url) {
        $key = 'services.'.$providerName.'.redirect';
        config()->set($key, $url);
    }

    /**
     * Redirects to the given provider's 
     * login screen
     *
     * @param string $providerName
     * @param string|null $redirectUrl
     * @return void
     */
    public function redirectToProviderLogin(string $providerName, ?string $redirectUrl = null) {
        if ($redirectUrl !== null) {
            $this->setRedirectUrl($providerName, $redirectUrl);
        }

        $driver = $this->getDriver($providerName);

        // we need to manually specify the scopes 
        // for Discord to work
        if ($providerName === SocialProvider::DISCORD) {
            $driver->scopes(['identity', 'email'])->redirect();
        }
        return $driver->redirect();
    }
    
    /**
     * Retrieves a user from the OAuth provider 
     * response and returns a SocaliteData model
     *
     * @return SocialiteData
     */
    public function getProviderResponse(string $providerName) : SocialiteData {
        $providerAccount = $this->getDriver($providerName)->user();

        if($providerAccount->getEmail() === null) {
            throw new UnsupportedProviderException('Provider ['.$this->providerName.'] does not give read access to an email address');
        }
        if($providerAccount->getId() === null) {
            throw new UnsupportedProviderException('Provider ['.$this->providerName.'] does not provide a unique identifier');
        }

        return new SocialiteData($providerName,
                                 $providerAccount->getEmail(),
                                 $providerAccount->name ?: "Hidden Name",
                                 $providerAccount->getId());
    }

    /**
     * Returns true if the user cancelled the
     * login process on the provider's screen
     *
     * @param Request $request
     * @return boolean
     */
    public function cancelled(Request $request) : bool {
        if ($request->get('denied')) {
            return true;
        }
        return false;
    }

}
