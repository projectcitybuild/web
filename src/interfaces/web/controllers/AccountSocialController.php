<?php

namespace Interfaces\Web\Controllers;

use Domains\Modules\Accounts\Repositories\AccountLinkRepository;
use Domains\Modules\Accounts\Models\Account;
use Application\Environment;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Domains\Library\OAuth\OAuthLoginHandler;
use Domains\Library\OAuth\Adapters\Discord\DiscordOAuthAdapter;
use Domains\Library\OAuth\Exceptions\UnsupportedOAuthAdapter;

class AccountSocialController extends WebController
{
    /**
     * @var AccountLinkRepository
     */
    private $linkRepository;

    /**
     * @var OAuthLoginHandler
     */
    private $oauthLoginHandler;

    /**
     * @var Auth
     */
    private $auth;


    public function __construct(AccountLinkRepository $linkRepository,
                                OAuthLoginHandler $oauthLoginHandler,
                                Auth $auth) 
    {
        $this->linkRepository = $linkRepository;
        $this->oauthLoginHandler = $oauthLoginHandler;
        $this->auth = $auth;
    }


    public function showView(Request $request)
    {
        $account = $request->user();
        
        if ($account === null) {
            throw new \Exception('Logged-in account is null');
        }

        $links = $account->linkedSocialAccounts->keyBy('provider_name');

        return view('front.pages.account.account-links', [
            'links' => $links,
        ]);
    }

    public function redirectToProvider(string $providerName)
    {
        try {
            $this->oauthLoginHandler->setProvider($providerName);
        } catch (UnsupportedOAuthAdapter $e) {
            abort(404);
        }
        
        $redirectUri = route('front.account.social.callback', $providerName);
        
        return $this->oauthLoginHandler->redirectToLogin($redirectUri);
    }

    public function handleProviderCallback(string $providerName, Request $request)
    {
        try {
            $this->oauthLoginHandler->setProvider($providerName);
        } catch (UnsupportedOAuthAdapter $e) {
            abort(404);
        }

        $account = $this->auth->user();
        if ($account === null) {
            throw new \Exception('Logged-in account is null');
        }

        $providerAccount = $this->oauthLoginHandler->getOAuthUser();
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

    public function deleteLink(string $providerName, Request $request)
    {
        try {
            $this->oauthLoginHandler->setProvider($providerName);
        } catch (UnsupportedOAuthAdapter $e) {
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
