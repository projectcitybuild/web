<?php
namespace App\Modules\Accounts\Services\Login;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory;
use App\Modules\Discourse\Services\Authentication\DiscoursePayload;
use Illuminate\Contracts\Auth\Guard as Auth;

class AccountManualLoginExecutor implements AccountLoginable {
    
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Factory
     */
    private $validator;

    /**
     * @var Auth
     */
    private $auth;


    public function __construct(Request $request, Factory $validator, Auth $auth) {
        $this->request = $request;
        $this->validator = $validator;
        $this->auth = $auth;
    }

    /**
     * Attempts to login to PCB with a normal POST
     * email and password
     *
     * @param string $nonce
     * @return DiscoursePayload
     */
    public function execute(string $nonce) : DiscoursePayload {
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

        return (new DiscoursePayload($nonce))
            ->setPcbId($account->getKey())
            ->setEmail($account->email);
    }

}