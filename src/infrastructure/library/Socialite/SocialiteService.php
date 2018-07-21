<?php
namespace Infrastructure\Library\Socialite;

use Laravel\Socialite\Facades\Socialite;
use Infrastructure\Library\Socialite\Exceptions\UnsupportedProviderException;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

class SocialiteService
{

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Logger
     */
    private $log;


    public function __construct(Request $request, Logger $logger)
    {
        $this->request = $request;
        $this->log = $logger;
    }
    

    private function getDriver(string $providerName)
    {
        return Socialite::driver($providerName);
    }

    private function setRedirectUrl(string $providerName, string $url)
    {
        $key = 'services.'.$providerName.'.redirect';
        config()->set($key, $url);

        $this->log->debug('OAuth redirect URL set to: '.$url);
    }

    /**
     * Redirects to the given provider's
     * login screen
     *
     * @param string $providerName
     * @param string|null $redirectUrl
     * @return void
     */
    public function redirectToProviderLogin(string $providerName, ?string $redirectUrl = null)
    {
        if ($redirectUrl !== null) {
            $this->setRedirectUrl($providerName, $redirectUrl);
        }

        $this->log->info('Redirecting user to provider login: '.$providerName);

        return $this->getDriver($providerName)->redirect();
    }
    
    /**
     * Retrieves a user from the OAuth provider
     * response and returns a SocaliteData model
     *
     * @return SocialiteData
     */
    public function getProviderResponse(string $providerName) : SocialiteData
    {
        $providerAccount = $this->getDriver($providerName)->user();

        $this->log->info('Received user from OAuth provider');
        $this->log->debug($providerAccount);

        if ($providerAccount->getEmail() === null) {
            throw new UnsupportedProviderException('Provider ['.$this->providerName.'] does not give read access to an email address');
        }
        if ($providerAccount->getId() === null) {
            throw new UnsupportedProviderException('Provider ['.$this->providerName.'] does not provide a unique identifier');
        }

        return new SocialiteData(
            $providerName,
                                 $providerAccount->getEmail(),
                                 $providerAccount->name ?: "Hidden Name",
                                 $providerAccount->getId()
        );
    }

    /**
     * Returns true if the user cancelled the
     * login process on the provider's screen
     *
     * @param Request $request
     * @return boolean
     */
    public function cancelled(Request $request) : bool
    {
        if ($request->get('denied')) {
            return true;
        }
        return false;
    }
}
