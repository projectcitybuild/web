<?php

namespace App\Http\Resources;

use App\Models\Account;
use Tests\TestCase;

class AccountResourceTest extends TestCase
{
    /**
     * @ticket GH-654
     */
    public function test_no_last_login_date()
    {
        $account = Account::factory()->withTimestamps()->neverLoggedIn()->make();
        $resource = (new AccountResource($account))->toArray(request());
        $this->assertNull($resource['last_login_at']);
    }
}
