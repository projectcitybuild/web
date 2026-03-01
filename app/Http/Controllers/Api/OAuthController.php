<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

final class OAuthController extends ApiController
{
    public function show(Request $request): array
    {
        $account = $request->user();

        return [
            'id' => $account->id,
            'email' => $account->email,
            'nickname' => $account->username,
            'name' => $account->username,
            'avatar' => null,
        ];
    }
}
