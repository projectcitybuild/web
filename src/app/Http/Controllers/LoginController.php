<?php

namespace App\Http\Controllers;

use App\Library\Discourse\Authentication\DiscourseLoginHandler;
use App\Library\Discourse\Exceptions\BadSSOPayloadException;
use App\Services\Login\LogoutService; 
use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use \Illuminate\View\View;
use App\Environment;
use App\Entities\Accounts\Repositories\AccountRepository;
use App\Http\WebController;
use Illuminate\Support\Facades\Log;

final class LoginController extends WebController
{
    /**
     * @var DiscourseLoginHandler
     */
    private $discourseLoginHandler;

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


    public function __construct(
        DiscourseLoginHandler $discourseLoginHandler,
        AccountRepository $accountRepository,
        LogoutService $logoutService,
        Auth $auth
    ) {
        $this->discourseLoginHandler = $discourseLoginHandler;
        $this->accountRepository = $accountRepository;
        $this->logoutService = $logoutService;
        $this->auth = $auth;
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
            Log::debug('Missing nonce or return key in session', ['session' => $request->session()]);
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
