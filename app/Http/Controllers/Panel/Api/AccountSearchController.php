<?php


namespace App\Http\Controllers\Panel\Api;


use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Resources\AccountResource;
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
