<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\APIController;
use App\Models\Account;
use Illuminate\Http\Request;

final class OAuthController extends APIController
{
    public function show(Request $request): array
    {
        /** @var Account $account */
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
