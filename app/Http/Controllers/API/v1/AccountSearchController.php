<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Account;
use Entities\Resources\AccountResource;
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
