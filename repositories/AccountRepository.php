<?php

namespace Repositories;

use App\Models\Account;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * @deprecated
 */
class AccountRepository
{
    public function getByEmail(string $email): ?Account
    {
        return Account::where('email', $email)->first();
    }
}
