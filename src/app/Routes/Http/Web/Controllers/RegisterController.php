<?php

namespace App\Routes\Http\Web\Controllers;

use Illuminate\Support\Facades\View;
use App\Routes\Http\Web\WebController;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as Validation;
use App\Modules\Accounts\Repositories\AccountRepository;
use Hash;

class RegisterController extends WebController {
    
    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var Hasher
     */
    private $hasher;


    public function __construct(
        Validation $validation, 
        AccountRepository $accountRepository
    ) {
        $this->validation = $validation;
        $this->accountRepository = $accountRepository;
    }

    public function showRegisterView() {
        return view('register');
    }

    public function register(Request $request) {
        $validator = $this->validation->make($request->all(), [
            'email'             => 'required|email|unique:accounts,email',
            'password'          => 'required|min:4',
            'password_confirm'  => 'required_with:password|same:password',
        ]);

        if($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator->errors())
                ->withInput();
        }

        $password = $request->get('password');
        $password = Hash::make($password);

        $account = $this->accountRepository->create(
            $request->get('email'),
            $password,
            $request->ip()
        );
    }
}
