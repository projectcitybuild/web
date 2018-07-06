<?php

namespace App\Api\Controllers;

use App\Api\ApiController;
use Illuminate\Validation\Factory as Validator;
use Illuminate\Http\Request;
use App\Support\Exceptions\UnauthorisedException;
use App\Modules\Accounts\Models\Account;
use GuzzleHttp\Client;
use Hash;
use Illuminate\Support\Facades\Cache;
use App\Support\Exceptions\BadRequestException;
use App\Support\Exceptions\ServerException;
use App\Modules\Discourse\Services\Api\DiscourseAdminApi;

class TempMinecraftController extends ApiController {

    /**
     * @var DiscourseAdminApi
     */
    private $adminApi;

    public function __construct(DiscourseAdminApi $adminApi) {
        $this->adminApi = $adminApi;
    }
    
    public function authenticate(Request $request, Validator $validation, Client $client) {
        $validator = $validation->make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $account = Account::where('email', $request->get('email'))->first();
        if($account === null) {
            throw new UnauthorisedException('invalid_credentials', 'Email and/or password is incorrect');
        }

        if(Hash::check($request->get('password'), $account->password) === false) {
            throw new UnauthorisedException('invalid_credentials', 'Email and/or password is incorrect');
        }

        return Cache::remember('minecraft.'.$request->get('email'), 5, function() use($request, $client) {
            return $this->fetch($request, $client);
        });
    }

    private function fetch(Request $request) {
        $result = $this->adminApi->fetchUsersByEmail($request->get('email'));
        if(count($result) === 0) {
            throw new ServerException('no_discourse_account', 'No matching Discourse account could be found. Please contact a staff member');
        }

        return [
            'data' => $result,
        ];
    }

}
