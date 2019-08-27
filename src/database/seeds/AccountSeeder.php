<?php

use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Models\UnactivatedAccount;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Account::class, 500)->create();
        factory(UnactivatedAccount::class, 50)->create();
    }
}
