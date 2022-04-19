<?php

namespace App\Http\Controllers\Panel\Api;

use Entities\Models\Eloquent\Account;
use Entities\Resources\AccountResource;
use Illuminate\Http\Request;

class AccountSearchController
{
    public function __invoke(Request $request)
    {
        $query = $request->input('query');
        $accounts = Account::search($query)->get();

        return AccountResource::collection($accounts);
    }
}
