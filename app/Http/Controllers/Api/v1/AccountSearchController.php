<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountSearchController
{
    public function __invoke(Request $request)
    {
        $query = $request->input('query');

        return Account::search($query)
            ->take(25)
            ->get();
    }
}
