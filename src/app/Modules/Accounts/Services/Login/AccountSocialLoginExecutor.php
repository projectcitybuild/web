<?php
namespace App\Modules\Accounts\Services\Login;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory;
use App\Modules\Discourse\Services\Authentication\DiscoursePayload;
use Illuminate\Contracts\Auth\Guard as Auth;

class AccountSocialLoginExecutor implements AccountLoginable {
    
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Auth
     */
    private $auth;


    public function __construct(Request $request, Auth $auth) {
        $this->request = $request;
        $this->auth = $auth;
    }

    public function execute(string $nonce) : DiscoursePayload {
        $providerUser = $this->socialAuthService
                ->setProvider($providerName)
                ->handleProviderResponse();

        // if user exists, log them into Discourse
        $account = $this->accountRepository->getByEmail($providerUser->getEmail());
        if($account !== null) {
            $this->auth->setUser($account);
    
            return (new DiscoursePayload($nonce))
                ->setPcbId($account->getKey())
                ->setEmail($account->email);
        }

        // otherwise redirect to create confirmation page
        // $social = [
        //     'email'     => $providerUser->getEmail(),
        //     'id'        => $providerUser->getId(),
        //     'name'      => $providerUser->name,
        //     'provider'  => 'google',
        // ];

        // $url = URL::temporarySignedRoute('front.login.social-register', now()->addMinutes(10), $social);

        // return view('register-oauth', [
        //     'social' => $social,
        //     'url'    => $url,
        // ]);

        return (new DiscoursePayload($nonce))
            ->setPcbId($account->getKey())
            ->setEmail($account->email);
    }

}