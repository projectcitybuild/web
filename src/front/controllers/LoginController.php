<?php

namespace Front\Controllers;

use App\Modules\Forums\Exceptions\BadSSOPayloadException;
use App\Modules\Accounts\Services\AccountSocialLinkService;
use App\Modules\Accounts\Services\Login\AccountLoginService;
use App\Modules\Accounts\Services\Login\AccountSocialLoginExecutor;
use App\Library\Discourse\Api\DiscourseUserApi;
use App\Library\Discourse\Api\DiscourseAdminApi;
use App\Library\Discourse\Authentication\DiscourseAuthService;
use App\Library\Discourse\Authentication\DiscoursePayload;
use Front\Requests\LoginRequest;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\Request;
use App\Modules\Accounts\Repositories\AccountRepository;
use App\Library\Socialite\SocialiteService;
use App\Modules\Accounts\Exceptions\InvalidDiscoursePayloadException;
use App\Modules\Accounts\Repositories\AccountLinkRepository;
use Illuminate\Database\Connection;
use App\Library\Socialite\SocialProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Support\Environment;
use Illuminate\Support\Facades\Log;

class LoginController extends WebController {

    /**
     * @var DiscourseAuthService
     */
    private $discourseAuthService;

    /**
     * @var DiscourseUserApi
     */
    private $discourseUserApi;

    /**
     * @var DiscourseAdminApi
     */
    private $discourseAdminApi;

    /**
     * @var SocialiteService
     */
    private $socialiteService;

    /**
     * @var AccountSocialLinkService
     */
    private $accountLinkService;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var AccountLinkRepository
     */
    private $accountLinkRepository;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Log
     */
    private $log;


    public function __construct(DiscourseAuthService $discourseAuthService, 
                                DiscourseUserApi $discourseUserApi,
                                DiscourseAdminApi $discourseAdminApi,
                                SocialiteService $socialiteService,
                                AccountSocialLinkService $accountLinkService,
                                AccountRepository $accountRepository,
                                AccountLinkRepository $accountLinkRepository,
                                Auth $auth,
                                Connection $connection,
                                Log $log) 
    {
        $this->discourseAuthService = $discourseAuthService;
        $this->discourseUserApi = $discourseUserApi;
        $this->discourseAdminApi = $discourseAdminApi;
        $this->socialiteService = $socialiteService;
        $this->accountLinkService = $accountLinkService;
        $this->accountRepository = $accountRepository;
        $this->accountLinkRepository = $accountLinkRepository;
        $this->auth = $auth;
        $this->connection = $connection;
        $this->log = $log;
    }

    public function showLoginView(Request $request) {
        if ($this->auth->check()) {
            $this->log->verbose('Already logged-in; redirecting...');
            return redirect()->route('front.home');
        }

        // login route should have a valid payload in the url
        // generated by discourse when being redirected here
        $sso        = $request->get('sso');
        $signature  = $request->get('sig');

        if($sso === null || $signature === null) {
            return redirect()->route('front.home');
        }

        // validate that the given signature matches the
        // payload when signed with our private key. This
        // prevents any payload tampering
        $isValidPayload = $this->discourseAuthService->isValidPayload($sso, $signature);
        if($isValidPayload === false) {
            $this->log->debug('Received invalid SSO payload (sso: '.$sso.' | sig: '.$signature);
            abort(400);
        }

        // ensure that the payload has all the necessary
        // data required to create a new payload after
        // authentication
        $payload = null;
        try {
            $payload = $this->discourseAuthService->unpackPayload($sso);

        } catch(BadSSOPayloadException $e) {
            $this->log->debug('Failed to unpack SSO payload (sso: '.$sso.' | sig: '.$signature);
            abort(400);
        }

        // store the nonce and return url in a session so
        // the user cannot access or tamper with it at any
        // point during authentication
        $request->session()->put([
            'discourse_nonce'   => $payload['nonce'],
            'discourse_return'  => $payload['return_sso_url'],
        ]);

        $this->log->verbose('Storing SSO data in session for login');

        return view('front.pages.login.login');
    }

    /**
     * Manual login with email and password via form post
     *
     * @param LoginRequest $request
     * @return void
     */
    public function login(LoginRequest $request) {
        $session = $request->session();

        $nonce     = $session->get('discourse_nonce');
        $returnUrl = $session->get('discourse_return');

        if($nonce === null || $returnUrl === null) {
            $this->log->debug('Missing nonce or return key in session...');
            $this->log->debug($session);
            throw new InvalidDiscoursePayloadException('`nonce` or `return` key missing in session');
        }

        $request->validated();

        $account = $this->auth->user();
        if ($account === null) {
            throw new \Exception('Account was null after authentication');
        }

        $payload = (new DiscoursePayload($nonce))
            ->setPcbId($account->getKey())
            ->setEmail($account->email)
            ->requiresActivation(false)
            ->build();
        
        $session->remove('discourse_nonce');
        $session->remove('discourse_return');   
    

        // generate new payload to send to discourse
        $payload    = $this->discourseAuthService->makePayload($payload);
        $signature  = $this->discourseAuthService->getSignedPayload($payload);

        // attach parameters to return url
        $endpoint   = $this->discourseAuthService->getRedirectUrl($returnUrl, $payload, $signature);

        $this->log->info('Logging in user: '.$account->getKey());

        return redirect()->to($endpoint);
    }


    private function isValidProvider(string $providerName) : bool {
        $providerName = strtolower($providerName);

        $allowedProviders = array_map(function($key) {
            return strtolower($key);
        }, SocialProvider::getKeys());
        
        return in_array($providerName, $allowedProviders);
    }

    public function redirectToProvider(string $providerName) {
        if (!$this->isValidProvider($providerName)) {
            abort(404);
        }

        $route = route('front.login.provider.callback', $providerName);
        
        // Laravel converts localhost into a local IP address, 
        // so we need to manually convert it back so that 
        // it matches the URLs registered in Twitter, Google, etc
        if (Environment::isDev()) {
            $route = str_replace('http://192.168.99.100/', 
                                 'http://localhost:3000/', 
                                 $route);

            $this->log->debug('Transformed OAuth redirect url: '.$route);
        }

        $this->log->verbose('Redirecting user to OAuth provider: '.$providerName);

        return $this->socialiteService->redirectToProviderLogin($providerName, $route);
    }

    public function handleProviderCallback(string $providerName, Request $request) {
        if (!$this->isValidProvider($providerName)) {
            abort(404);
        }

        if ($request->get('denied')) {
            return redirect()->route('front.home');
        }

        $this->log->verbose('Received user from OAuth provider...');

        $session = $request->session();

        $nonce     = $session->get('discourse_nonce');
        $returnUrl = $session->get('discourse_return');

        if($nonce === null || $returnUrl === null) {
            throw new InvalidDiscoursePayloadException('`nonce` or `return` key missing in session');
        }

        
        $providerAccount = $this->socialiteService->getProviderResponse($providerName);

        $this->log->debug($providerAccount);

        $existingLink = $this->accountLinkRepository->getByProviderAccount($providerName, $providerAccount->getId());

        if ($existingLink === null) {
            
            // if an account link doesn't exist, we need to
            // check that the email is not already in use
            // by a different account, because PCB and Discourse
            // accounts must have a unique email
            $existingAccount = $this->accountRepository->getByEmail($providerAccount->getEmail());
            if ($existingAccount !== null) {
                $this->log->verbose('Account with email ('.$providerAccount->getEmail().') already exists; showing error to user');

                return view('front.pages.register.register-oauth-failed', [
                    'email' => $providerAccount->getEmail(),
                ]);
            }

            // otherwise send them to the register confirmation
            // view using their provider account data
            $url = URL::temporarySignedRoute('front.login.social-register', 
                                             now()->addMinutes(10), 
                                             $providerAccount->toArray());

            $this->log->debug('Generating OAuth register URL: '.$url);

            return view('front.pages.register.register-oauth', [
                'social' => $providerAccount->toArray(),
                'url'    => $url,
            ]);
        }

        // otherwise login the account linked to
        // the provider account's id
        if ($existingLink->account === null) {
            throw new \Exception('Account link is missing an account');
        }
        
        $account = $existingLink->account;

        $this->auth->setUser($account);

        $session->remove('discourse_nonce');
        $session->remove('discourse_return');     

        $payload = (new DiscoursePayload($nonce))
            ->setPcbId($account->getKey())
            ->setEmail($account->email)
            ->requiresActivation(false)
            ->build();

    
        $payload    = $this->discourseAuthService->makePayload($payload);
        $signature  = $this->discourseAuthService->getSignedPayload($payload);

        $url = $this->discourseAuthService->getRedirectUrl($returnUrl, $payload, $signature);

        $this->log->info('Logging in PCB user ('.$account->getKey().') via OAuth');
        $this->log->debug($payload);
        $this->log->debug($providerAccount->toArray());

        return redirect()->to($url);
    }

    public function createSocialAccount(Request $request) {
        $providerEmail = $request->get('email');
        $providerId    = $request->get('id');
        $providerName  = $request->get('provider');

        if($providerEmail === null) {
            abort(400, 'Missing social email');
        }
        if($providerId === null) {
            abort(400, 'Missing social id');
        }
        if($providerName === null) {
            abort(400, 'Missing social provider name');
        }
        
        $session = $request->session();

        $nonce     = $session->get('discourse_nonce');
        $returnUrl = $session->get('discourse_return');

        if($nonce === null || $returnUrl === null) {
            throw new InvalidDiscoursePayloadException('`nonce` or `return` key missing in session');
        }

        
        $accountLink = $this->accountLinkRepository->getByProviderAccount($providerName, $providerId);
        if($accountLink !== null) {
            throw new \Exception('Attempting to create PCB account via OAuth, but OAuth account already exists');
        }

        $account = null;
        $this->connection->beginTransaction();
        try {
             // create a PCB account for the user
            $account = $this->accountRepository->create($providerEmail,
                                                        Hash::make(time()),
                                                        null,
                                                        Carbon::now());

            // and then create an account link to it
            $accountLink = $this->accountLinkRepository->create($account->getKey(),
                                                                $providerName, 
                                                                $providerId,
                                                                $providerEmail);

            $this->connection->commit();

        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

        if ($account === null) {
            throw new \Exception('Account is null after OAuth creation');
        }

        $payload = (new DiscoursePayload($nonce))
            ->setPcbId($account->getKey())
            ->setEmail($account->email)
            ->requiresActivation(false)
            ->build();
        
        $session->remove('discourse_nonce');
        $session->remove('discourse_return');   
    

        // generate new payload to send to discourse
        $payload    = $this->discourseAuthService->makePayload($payload);
        $signature  = $this->discourseAuthService->getSignedPayload($payload);

        // attach parameters to return url
        $endpoint   = $this->discourseAuthService->getRedirectUrl($returnUrl, $payload, $signature);

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
    public function logoutFromDiscourse(Request $request) {
        $this->auth->logout();
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
    public function logout(Request $request) {
        if(!$this->auth->check()) {
            return redirect()->route('front.home');
        }

        $externalId = $this->auth->id();
        $result = $this->discourseUserApi->fetchUserByPcbId($externalId);

        $user = $result['user'];
        if($user === null) {
            throw new \Exception('Discourse logout api response did not have a `user` key');
        }

        $this->log->info('Logging out user: '.$externalId);
        $this->log->debug('Discourse ID: '.$user['id'].' | Discourse username: '.$user['username']);
        $this->log->debug($result);

        $this->discourseAdminApi->requestLogout($user['id'], $user['username']);

        $this->auth->logout();
        
        return redirect()->route('front.home');
    }

}
