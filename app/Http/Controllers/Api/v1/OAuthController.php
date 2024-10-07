<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\ApiController;
use App\Models\Account;
use Illuminate\Http\Request;

final class OAuthController extends ApiController
{
    public function show(Request $request): array
    {
        $account = $request->user();

        return [
            'id' => $account->getKey(),
            'email' => $account->email,
            'nickname' => $account->username,
            'name' => $account->username,
            'avatar' => null,
        ];
    }
}
