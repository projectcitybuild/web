<?php

namespace App\Http\Requests;

use App\Entities\Accounts\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Library\RateLimit\TokenRate;
use App\Library\RateLimit\TokenBucket;
use App\Library\RateLimit\Storage\SessionTokenStorage;

final class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'email'     => 'required',
            'password'  => 'required',
        ];
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }
}
