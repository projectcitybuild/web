<?php
namespace Front\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array {
        return [
            'email'                 => 'required|email|unique:accounts,email',
            'password'              => 'required|min:8',    // discourse min is 8 or greater
            'password_confirm'      => 'required_with:password|same:password',
            'g-recaptcha-response'  => 'required|recaptcha',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool {
        return true;
    }

}