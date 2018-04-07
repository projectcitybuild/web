<?php

namespace App\Routes\Http\Api\Controllers;

use Illuminate\Validation\Factory as Validator;
use Illuminate\Http\Request;
use App\Routes\Api\ApiController;
use App\Shared\Exceptions\UnauthorisedException;
use App\Modules\Accounts\Models\Account;
use GuzzleHttp\Client;
use Hash;
use Illuminate\Support\Facades\Cache;
use App\Shared\Exceptions\BadRequestException;
use App\Shared\Exceptions\ServerException;

class TempMinecraftController extends ApiController {
    
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

    private function fetch(Request $request, Client $client) {
        // fetch username from Discourse
        $apiKey = ENV('DISCOURSE_API_KEY');
        $url = 'http://forums.projectcitybuild.com/admin/users/list/all.json?api_key='.$apiKey.'&api_username=Andy&email='.$request->get('email');
        
        $response = $client->get($url);
        $result = json_decode($response->getBody(), true);

        if(count($result) === 0) {
            throw new ServerException('no_discourse_account', 'No matching Discourse account could be found. Please contact a staff member');
        }

        // fetch group from Discourse via username
        $discourseUser = $result[0];
        $username = $discourseUser['username'];

        $userResponse = $client->get('http://forums.projectcitybuild.com/users/'.$username.'.json');
        $userResult = json_decode($userResponse->getBody(), true);

        $user = $userResult['user'];
        if($user === null || count($user) === 0) {
            throw new ServerException('search_failed', 'Could not retrieve Discourse account details. Please contact a staff member');
        }

        $groups = $user['groups'];
        
        return [
            'data' => $groups,
        ];
    }

}
