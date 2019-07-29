<?php

namespace App\Http\Controllers;

use App\Library\Discourse\Api\DiscourseAdminApi;
use App\Library\Discourse\Api\DiscourseUserApi;
use App\Library\Discourse\Authentication\DiscourseLoginHandler;
use App\Library\Discourse\Exceptions\BadSSOPayloadException;
use App\Entities\Accounts\Repositories\AccountRepository;
use App\Services\Login\LogoutService; 
use App\Http\Requests\LoginRequest;
use App\Http\WebController;
use App\Entities\Environment;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

final class LoginController extends WebController
{
    /**
     * @var DiscourseAdminApi
     */
    private $discourseAdminApi;

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
        DiscourseAdminApi $discourseAdminApi,
        AccountRepository $accountRepository,
        LogoutService $logoutService,
        Auth $auth
    ) {
        $this->discourseAdminApi = $discourseAdminApi;
        $this->accountRepository = $accountRepository;
        $this->logoutService = $logoutService;
        $this->auth = $auth;
    }

    public function create()
    {
        return view('front.pages.login.login');
    }

    public function store(LoginRequest $request)
    {
        $account  = $this->auth->user();

        // Set the user's nickname from Discourse if it isn't already
        if ($account->username == null) {
            $discourseUser = $this->discourseAdminApi->fetchUserByEmail($account->email);
            $account->username = $discourseUser["username"];
            $account->save();
        }

        // Redirect back to the intended page
        // If the user is just logging in, perform discourse SSO
        return redirect()->intended(route('front.sso.discourse'));
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
