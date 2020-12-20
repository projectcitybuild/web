<?php

namespace Database\Seeders;

use App\Entities\Accounts\Models\Account;
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
    }
}
