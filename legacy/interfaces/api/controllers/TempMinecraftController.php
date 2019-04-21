<?php

namespace Interfaces\Api\Controllers;

use Interfaces\Api\ApiController;
use Illuminate\Validation\Factory as Validator;
use Illuminate\Http\Request;
use Application\Exceptions\UnauthorisedException;
use Entities\Accounts\Models\Account;
use GuzzleHttp\Client;
use Hash;
use Illuminate\Support\Facades\Cache;
use Application\Exceptions\BadRequestException;
use Application\Exceptions\ServerException;
use Domains\Library\Discourse\Api\DiscourseAdminApi;
use Entities\Accounts\Resources\AccountResource;

class TempMinecraftController extends ApiController
{
    public function authenticate(Request $request, Validator $validation, Client $client)
    {
        $validator = $validation->make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        if ($validator->failed()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $account = Account::where('email', $request->get('email'))->first();
        if ($account === null) {
            throw new UnauthorisedException('invalid_credentials', 'Email and/or password is incorrect');
        }

        if (Hash::check($request->get('password'), $account->password) === false) {
            throw new UnauthorisedException('invalid_credentials', 'Email and/or password is incorrect');
        }

        // force group load
        $account->groups;

        return [
            'data' => new AccountResource($account),
        ];
    }
}
