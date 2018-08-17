<?php

namespace Interfaces\Web\Controllers;

use Domains\Library\Discourse\Authentication\DiscourseLoginHandler;
use Domains\Library\Discourse\Exceptions\BadSSOPayloadException;
use Domains\Library\OAuth\OAuthLoginHandler;
use Domains\Library\OAuth\Exceptions\UnsupportedOAuthAdapter;
use Domains\Services\Login\LoginAccountCreationService;
use Domains\Services\Login\SocialEmailInUseException;
use Domains\Services\Login\LogoutService; 
use Interfaces\Web\Requests\LoginRequest;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Log\Logger;
use Illuminate\Http\Request;

class LoginController extends WebController
{
    /**
     * @var DiscourseLoginHandler
     */
    private $discourseLoginHandler;

    /**
     * @var OAuthLoginHandler
     */
    private $oauthLoginHandler;

    /**
     * @var LoginAccountCreationService
     */
    private $accountCreationService;

    /**
     * @var LogoutService
     */
    private $logoutService;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Logger
     */
    private $log;


    public function __construct(DiscourseLoginHandler $discourseLoginHandler,
                                OAuthLoginHandler $oauthLoginHandler,
                                LoginAccountCreationService $accountCreationService,
                                LogoutService $logoutService,
                                Auth $auth,
                                Logger $logger)
    {
        $this->discourseLoginHandler = $discourseLoginHandler;
        $this->oauthLoginHandler = $oauthLoginHandler;
        $this->accountCreationService = $accountCreationService;
        $this->logoutService = $logoutService;
        $this->auth = $auth;
        $this->log = $logger;
    }

    public function loginOrShowForm()
    {
        if ($this->auth->check() === false) {
            return view('front.pages.login.login');
        }

        try {
            $account  = $this->auth->user();
            $endpoint = $this->discourseLoginHandler->getRedirectUrl($account->getKey(), 
                                                                     $account->email);
            return redirect()->to($endpoint);

        } catch (BadSSOPayloadException $e) {
            $this->log->debug('Missing nonce or return key in session...', ['session' => $request->session()]);
            throw $e;
        }
    }

    /**
     * Handles a login request sent from the login form
     *
     * @param LoginRequest $request
     * @return void
     */
    public function login(LoginRequest $request)
    {
        $this->loginOrShowForm();
    }

    /**
     * Redirects to the login screen of an OAuth provider
     *
     * @param string $providerName
     * @return RedirectResponse
     */
    public function redirectToProvider(string $providerName)
    {
        try {
            $this->oauthLoginHandler->setProvider($providerName);
        } catch (UnsupportedOAuthAdapter $e) {
            abort(404);
        }

        $redirectUri = route('front.login.provider.callback', $providerName);

        return $this->oauthLoginHandler->redirectToLogin($redirectUri);
    }

    /**
     * Receives a payload from logging-in to an OAuth provider
     * and then either:
     * 
     * 1. Logs the user in if they have an account, or
     * 2. Redirects the user to a page to confirm creating
     *    a new account
     *
     * @param string $providerName
     * @param Request $request
     * @return void
     */
    public function handleProviderCallback(string $providerName, Request $request)
    {
        try {
            $this->oauthLoginHandler->setProvider($providerName);
        } catch (UnsupportedOAuthAdapter $e) {
            abort(404);
        }

        if ($request->get('denied')) {
            return redirect()->route('front.home');
        }

        $providerAccount = $this->oauthLoginHandler->getOAuthUser();

        $accountExists = false;
        try {
            $accountExists = $this->accountCreationService->hasAccountLink($providerAccount);
        } catch (SocialEmailInUseException $e) {
            // if no account link exists, but the provider account's
            // email address is already in use, we cannot proceed.
            // PCB/Discourse accounts must have a unique email.
            return view('front.pages.register.register-oauth-failed', [
                'email' => $providerAccount->getEmail(),
            ]);
        }

        if ($accountExists) {
            $account = $this->accountCreationService->getAccount();

            $this->auth->setUser($account);

            $endpoint = $this->discourseLoginHandler->getLoginRedirectUrl($account->getKey(), $account->email);
            return redirect()->to($endpoint);
        }
        
        $registerUrlPayload = $this->accountCreationService->generateSignedRegisterUrl($providerAccount);

        return view('front.pages.register.register-oauth', $registerUrlPayload);
    }

    /**
     * Creates a PCB account with a 3rd party account link
     *
     * @param Request $request
     * @return void
     */
    public function createSocialAccount(Request $request)
    {
        $providerEmail = $request->get('email');
        $providerId    = $request->get('id');
        $providerName  = $request->get('provider');

        $this->accountCreationService->createAccountWithLink($providerEmail,
                                                             $providerId,
                                                             $providerName);

        $endpoint = $this->discourseLoginHandler->getLoginRedirectUrl($account->getKey(), $account->email);

        return redirect()->to($endpoint);
    }

    /**
     * Logs out the current PCB account
     *
     * (called from Discourse)
     *
     * @param Request $request
     * @return void
     */
    public function logoutFromDiscourse(Request $request)
    {
        $this->logoutService->logoutOfPCB();

        return redirect()->route('front.home');
    }

    /**
     * Logs out the current PCB account and
     * its associated Discourse account
     *
     * (called from this site)
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        $this->logoutService->logoutOfDiscourseAndPcb();
        
        return redirect()->route('front.home');
    }
}
