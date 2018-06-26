<?php
namespace App\Modules\Accounts\Services\Login;

use Illuminate\Contracts\Validation\Factory;
use App\Modules\Discourse\Services\Authentication\DiscoursePayload;
use Illuminate\Contracts\Auth\Guard as Auth;
use App\Modules\Discourse\Services\Authentication\DiscourseAuthService;

class AccountManualLoginExecutor extends AbstractAccountLogin {

    /**
     * @var Factory
     */
    private $validator;

    /**
     * @var Auth
     */
    private $auth;


    public function __construct(DiscourseAuthService $discourseAuthService, Factory $validator, Auth $auth) {
        parent::__construct($discourseAuthService);
        
        $this->validator = $validator;
        $this->auth = $auth;
    }

    protected function execute(string $nonce, string $returnUrl) {
        $validator = $this->validator->make($this->request->all(), [
            'email'     => 'required',
            'password'  => 'required',
        ]);

        $validator->after(function($validator) {
            $credentials = [
                'email'     => $this->request->get('email'),
                'password'  => $this->request->get('password'),
            ];
            if($this->auth->attempt($credentials, true) === false) {
                $validator->errors()->add('error', 'Email or password is incorrect');
            }
        });

        if($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator->errors())
                ->withInput();
        }

        $account = $this->auth->user();

        $payload = (new DiscoursePayload($nonce))
            ->setPcbId($account->getKey())
            ->setEmail($account->email)
            ->requiresActivation(false);
        
        $this->invalidateSessionData();

        return $this->redirectToEndpoint($payload);
    }

}