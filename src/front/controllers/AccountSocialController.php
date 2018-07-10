<?php

namespace Front\Controllers;

use Illuminate\Support\Facades\View;
use App\Modules\Accounts\Repositories\AccountLinkRepository;
use Illuminate\Http\Request;
use App\Library\Socialite\SocialiteService;

class AccountSocialController extends WebController {

    /**
     * @var AccountLinkRepository
     */
    private $linkRepository;

    /**
     * @var SocialiteService
     */
    private $socialiteService;

    public function __construct(AccountLinkRepository $linkRepository, 
                                SocialiteService $socialiteService) 
    {
        $this->linkRepository = $linkRepository;
        $this->socialiteService = $socialiteService;
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

    public function redirectToFacebook() {
        return $this->redirectToProvider(SocialiteService::FACEBOOK);
    }

    public function redirectToTwitter() {
        return $this->redirectToProvider(SocialiteService::TWITTER);
    }

    public function redirectToGoogle() {
        return $this->redirectToProvider(SocialiteService::GOOGLE);
    }
    
    private function redirectToProvider(string $providerName) {
        $redirectRoute = route('front.account.social.'.$providerName.'.callback');

        return $this->socialiteService
            ->setRedirectUrl($redirectRoute)
            ->setProvider($providerName)
            ->redirectToProviderLogin();
    }


    public function handleFacebookCallback(Request $request) {
        return $this->handleProviderCallback(SocialiteService::FACEBOOK, $request);
    }
    public function handleGoogleCallback(Request $request) {
        return $this->handleProviderCallback(SocialiteService::GOOGLE, $request);
    }
    public function handleTwitterCallback(Request $request) {
        return $this->handleProviderCallback(SocialiteService::TWITTER, $request);
    }

    private function handleProviderCallback(string $providerName, Request $request) {
        if ($request->get('denied')) {
            return redirect()->route('front.account.social');
        }

        $account = $request->user;
        if ($account === null) {
            throw new \Exception('Logged-in account is null');
        }

        $providerAccount = $this->socialiteService
            ->setProvider($providerName)
            ->getProviderResponse();

        $existingAccount = $this->linkRepository->getByProviderAccount($providerAccount->getName(), 
                                                                       $providerAccount->getId());

        if ($existingAccount !== null && $existingAccount->account_id !== $account->getKey()) {
            return route('front.pages.account.account-links')
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

        return view('front.pages.account.account-links')
            ->with(['success' => 'Successfully linked account']);
    }

}
