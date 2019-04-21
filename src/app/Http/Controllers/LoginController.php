<?php

namespace App\Http\Controllers;

use App\Library\Discourse\Authentication\DiscourseLoginHandler;
use App\Library\Discourse\Exceptions\BadSSOPayloadException;
use App\Library\OAuth\OAuthLoginHandler;
use App\Services\Login\LoginAccountCreationService;
use App\Services\Login\LogoutService; 
use Interfaces\Web\Requests\LoginRequest;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Log\Logger;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use \Illuminate\View\View;
use App\Services\Login\Exceptions\SocialAccountAlreadyInUseException;
use App\Environment;
use App\Entities\Accounts\Repositories\AccountRepository;
use App\Library\OAuth\Exceptions\OAuthSessionExpiredException;
use App\Http\WebController;

final class LoginController extends WebController
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
     * @var AccountRepository
     */
    private $accountRepository;

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


    public function __construct(
        DiscourseLoginHandler $discourseLoginHandler,
        OAuthLoginHandler $oauthLoginHandler,
        LoginAccountCreationService $accountCreationService,
        AccountRepository $accountRepository,
        LogoutService $logoutService,
        Auth $auth,
        Logger $logger
    ) {
        $this->discourseLoginHandler = $discourseLoginHandler;
        $this->oauthLoginHandler = $oauthLoginHandler;
        $this->accountCreationService = $accountCreationService;
        $this->accountRepository = $accountRepository;
        $this->logoutService = $logoutService;
        $this->auth = $auth;
        $this->log = $logger;
    }

    /**
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function loginOrShowForm(Request $request)
    {
        if ($this->auth->check() === false) 
        {
            return view('front.pages.login.login');
        }
        try 
        {
            $account  = $this->auth->user();
            $endpoint = $this->discourseLoginHandler->getRedirectUrl(
                $account->getKey(), 
                $account->email
            );
            return $this->goToDiscourseWithLoginPayload($endpoint);
        } 
        catch (BadSSOPayloadException $e) 
        {
            $this->log->debug('Missing nonce or return key in session...', ['session' => $request->session()]);
            throw $e;
        }
    }

    /**
     * Handles a login request sent from the login form
     *
     * @param LoginRequest $request
     * @return RedirectResponse|View
     */
    public function login(LoginRequest $request)
    {
        return $this->loginOrShowForm($request);
    }

    /**
     * Redirects to the login screen of an OAuth provider
     *
     * @param string $providerName
     * @return RedirectResponse
     */
    public function redirectToProvider(string $providerName) : RedirectResponse
    {
        $callbackUri = route('front.login.provider.callback', $providerName);

        return $this->oauthLoginHandler->redirectToLogin(
            $providerName, 
            $callbackUri
        );
    }

    /**
     * Receives a payload from logging-in to an OAuth provider
     * and then either:
     * 
     * 1. Logs the user in if they have an account, or
     * 2. Redirects the user to a page to confirm creating a new account
     *
     * @param string $providerName
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function handleProviderCallback(string $providerName, Request $request)
    {
        // if the user cancels the login from the OAuth login screen, the
        // provider gives us a `denied` URL parameter
        if ($request->get('denied')) 
        {
            return redirect()->route('front.home');
        }

        $providerAccount = null;
        try
        {
            $providerAccount = $this->oauthLoginHandler->getOAuthUser($providerName);
        }
        catch (OAuthSessionExpiredException $e)
        {
            return response()->route('front.login');
        }

        $associatedAccount = $this->accountCreationService->getLinkedAccount($providerAccount);

        $isProviderAccountAssociated = $associatedAccount !== null;

        if (!$isProviderAccountAssociated)
        {
            // if no account link exists (ie. no one has associated the social account
            // ID and provider type to their PCB account yet), we need to check that the 
            // social account's email address is not already in use by another PCB account.
            //
            // Discourse and PCB require a unique email address, therefore the account
            // creation should fail
            $accountWithSameEmail = $this->accountRepository->getByEmail($providerAccount->getEmail());

            if ($accountWithSameEmail !== null) {
                $this->log->debug('Account with email ('.$providerAccount->getEmail().') already exists');
                
                return view('front.pages.register.register-oauth-failed', [
                    'email' => $providerAccount->getEmail(),
                ]);
            }

            // otherwise, let the user register a new PCB account with the provider
            // account's information (and associate it with the new PCB account)
            $registerUrlPayload = $this->accountCreationService->generateSignedRegisterUrl($providerAccount);
            return view('front.pages.register.register-oauth', $registerUrlPayload);
        }
        else
        {
            // or if the provider account is associated, proceed with login
            $this->auth->setUser($associatedAccount);

            $endpoint = $this->discourseLoginHandler->getRedirectUrl(
                $associatedAccount->getKey(), 
                $associatedAccount->email
            );

            return $this->goToDiscourseWithLoginPayload($endpoint);
        }      
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

        $account = null;
        try 
        {
            $account = $this->accountCreationService->createAccountWithLink(
                $providerEmail,
                $providerId,
                $providerName
            );
        }
        catch (SocialAccountAlreadyInUseException $e)
        {
            // even if the email address is not registered to any PCB account, 
            // we cannot proceed if a different account is already associated with
            // this social account id
            return view('front.pages.register.register-oauth-failed', [
                'email' => $providerEmail,
            ]);
        }

        $endpoint = $this->discourseLoginHandler->getRedirectUrl(
            $account->getKey(), 
            $account->email
        );

        return $this->goToDiscourseWithLoginPayload($endpoint);
    }

    private function goToDiscourseWithLoginPayload(string $redirectUri) : RedirectResponse
    {
        if (Environment::isDev())
        {
            abort(403, 'Redirect to Discourse disabled in DEV');
        }
        return redirect()->to($redirectUri);
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
        $this->logoutService->logoutOfDiscourseAndPCB();
        
        return redirect()->route('front.home');
    }
}
