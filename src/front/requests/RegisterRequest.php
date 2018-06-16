<?php
namespace Front\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest {

    public function rules() {
        return [
            'email'                 => 'required|email|unique:accounts,email',
            'password'              => 'required|min:8',    // discourse min is 8 or greater
            'password_confirm'      => 'required_with:password|same:password',
            'g-recaptcha-response'  => 'required|recaptcha',
        ];
    }

}