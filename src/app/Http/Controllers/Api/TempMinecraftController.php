<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiController;
use Illuminate\Validation\Factory as Validator;
use Illuminate\Http\Request;
use App\Exceptions\Http\UnauthorisedException;
use App\Entities\Eloquent\Accounts\Models\Account;
use GuzzleHttp\Client;
use Hash;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\Http\BadRequestException;
use App\Exceptions\Http\ServerException;
use App\Library\Discourse\Api\DiscourseAdminApi;
use App\Entities\Eloquent\Accounts\Resources\AccountResource;

/**
 * Obsoleted as of 1.12.0
 *
 * @deprecated 1.11.0 Use the new authentication flow provided by MinecraftAuthenticationController
 */
final class TempMinecraftController extends ApiController
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

        if (!$account->activated) {
            throw new UnauthorisedException('invalid_credentials', 'Your account hasn\'t been activated.');
        }

        // force group load
        $account->groups;

        return [
            'data' => new AccountResource($account),
        ];
    }
}
