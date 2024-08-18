<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountSearchController
{
    public function __invoke(Request $request)
    {
        $query = $request->input('query');

        $accounts = Account::search($query)
            ->take(25)
            ->get();

        return AccountResource::collection($accounts);
    }
}
