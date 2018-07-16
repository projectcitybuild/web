<?php

namespace Front\Controllers;

use Illuminate\Support\Facades\View;
use App\Modules\Accounts\Repositories\AccountLinkRepository;
use Illuminate\Http\Request;
use App\Library\Socialite\SocialiteService;
use App\Library\Socialite\SocialProvider;
use App\Modules\Accounts\Models\Account;
use App\Support\Environment;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Library\Discord\OAuth\DiscordOAuthService;

class AccountSocialController extends WebController {

    /**
     * @var AccountLinkRepository
     */
    private $linkRepository;

    /**
     * @var SocialiteService
     */
    private $socialiteService;

    /**
     * @var DiscordOAuthService
     */
    private $discordOAuthService;

    /**
     * @var Auth
     */
    private $auth;


    public function __construct(AccountLinkRepository $linkRepository, 
                                SocialiteService $socialiteService,
                                DiscordOAuthService $discordOAuthService,
                                Auth $auth) 
    {
        $this->linkRepository = $linkRepository;
        $this->socialiteService = $socialiteService;
        $this->discordOAuthService = $discordOAuthService;
        $this->auth = $auth;
    }


    public function showView(Request $request) {
        $account = $request->user();
        
        if ($account === null) {
            throw new \Exception('Logged-in account is null');
        }

        $links = $account->linkedSocialAccounts->keyBy('provider_name');

        return view('front.pages.account.account-links', [
            'links' => $links,
        ]);
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
        
        $route = route('front.account.social.callback', $providerName);
        
        // Laravel converts localhost into a local IP address, 
        // so we need to manually convert it back so that 
        // it matches the URLs registered in Twitter, Google, etc
        if (Environment::isDev()) {
            $route = str_replace('http://192.168.99.100/', 
                                 'http://localhost:3000/', 
                                 $route);
        }

        return $this->socialiteService->redirectToProviderLogin($providerName, $route);
    }

    public function handleProviderCallback(string $providerName, Request $request) {
        if (!$this->isValidProvider($providerName)) {
            abort(404);
        }

        $account = $this->auth->user();
        if ($account === null) {
            throw new \Exception('Logged-in account is null');
        }

        if ($this->socialiteService->cancelled($request)) {
            return redirect()->route('front.account.social');
        }

        $providerAccount = $this->socialiteService->getProviderResponse($providerName);

        $existingAccount = $this->linkRepository->getByProviderAccount($providerAccount->getName(), 
                                                                       $providerAccount->getId());

        if ($existingAccount !== null && $existingAccount->account_id !== $account->getKey()) {
            return redirect()
                ->route('front.account.social')
                ->withErrors('error', 'That account is already in use by a different PCB account');
        }

        $hasLink = $this->linkRepository->getByUserAndProvider($account->getKey(), $providerName);
        if ($hasLink) {
            $this->linkRepository->update($account->getKey(),
                                          $providerName, 
                                          $providerAccount->getId(), 
                                          $providerAccount->getEmail());
        } else {
            $this->linkRepository->create($account->getKey(),
                                          $providerName,
                                          $providerAccount->getId(),
                                          $providerAccount->getEmail());
        }

        return redirect()
            ->route('front.account.social')
            ->with(['success' => 'Successfully linked account']);
    }

    public function redirectToDiscord() {
        $route = route('front.account.social.callback.discord');
        
        // Laravel converts localhost into a local IP address, 
        // so we need to manually convert it back so that 
        // it matches the URLs registered in Twitter, Google, etc
        if (Environment::isDev()) {
            $route = str_replace('http://192.168.99.100/', 
                                 'http://localhost:3000/', 
                                 $route);
        }

        return $this->discordOAuthService->redirectToProvider($route);
    }

    public function handleDiscordCallback(Request $request) {
        $account = $this->auth->user();
        if ($account === null) {
            throw new \Exception('Logged-in account is null');
        }

        if ($this->socialiteService->cancelled($request)) {
            return redirect()->route('front.account.social');
        }

        $providerAccount = $this->discordOAuthService->getProviderAccount();

        $existingAccount = $this->linkRepository->getByProviderAccount('discord', 
                                                                       $providerAccount->getId());

        if ($existingAccount !== null && $existingAccount->account_id !== $account->getKey()) {
            return redirect()
                ->route('front.account.social')
                ->withErrors('error', 'That account is already in use by a different PCB account');
        }

        $hasLink = $this->linkRepository->getByUserAndProvider($account->getKey(), 'discord');
        if ($hasLink) {
            $this->linkRepository->update($account->getKey(),
                                          'discord', 
                                          $providerAccount->getId(), 
                                          $providerAccount->getEmail());
        } else {
            $this->linkRepository->create($account->getKey(),
                                          'discord',
                                          $providerAccount->getId(),
                                          $providerAccount->getEmail());
        }

        return redirect()
            ->route('front.account.social')
            ->with(['success' => 'Successfully linked account']);
    }

    public function deleteLink(string $providerName, Request $request) {
        if (!$this->isValidProvider($providerName)) {
            abort(404);
        }

        $account = $this->auth->user();
        
        $linkedAccount = $account->linkedSocialAccounts()
            ->where('provider_name', $providerName)
            ->first();

        if ($linkedAccount === null) {
            abort(401);
        }

        $linkedAccount->delete();

        return redirect()
            ->route('front.account.social')
            ->with(['success' => 'Successfully deleted linked account']);
    }

}
