<?php
namespace App\Modules\Accounts\Services\Login;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory;
use App\Modules\Discourse\Services\Authentication\DiscoursePayload;
use Illuminate\Contracts\Auth\Guard as Auth;
use App\Modules\Accounts\Services\AccountSocialAuthService;
use App\Modules\Accounts\Repositories\AccountRepository;
use App\Modules\Discourse\Services\Authentication\DiscourseAuthService;

class AccountSocialLoginExecutor extends AbstractAccountLogin {

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var AccountSocialAuthService
     */
    private $socialAuthService;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var string
     */
    private $providerName;


    public function __construct(
        DiscourseAuthService $discourseAuthService,
        Auth $auth,
        AccountSocialAuthService $socialAuthService, 
        AccountRepository $accountRepository
    ) {
        parent::__construct($discourseAuthService);

        $this->auth = $auth;
        $this->socialAuthService = $socialAuthService;
        $this->accountRepository = $accountRepository;
    }

    public function setProvider(string $providerName) : AccountSocialLoginExecutor {
        $this->providerName = $providerName;
        return $this;
    }

    protected function execute(string $nonce, string $returnUrl) {
        $provider = $this->socialAuthService
                ->setProvider($this->providerName)
                ->handleProviderResponse();

        // if user does not exist, redirect them to a
        // account creation confirmation page
        $account = $this->accountRepository->getByEmail($provider->getEmail());
        if($account === null) {
            $url = URL::temporarySignedRoute('front.login.social-register', now()->addMinutes(10), 
                $provider->toArray()
            );

            return view('register-oauth', [
                'social' => $provider->toArray(),
                'url'    => $url,
            ]);
        }

        $this->auth->setUser($account);

        $payload = (new DiscoursePayload($nonce))
            ->setPcbId($account->getKey())
            ->setEmail($account->email);

        $this->invalidateSessionData();

        return $this->redirectToEndpoint($payload);
    }

}