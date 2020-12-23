<?php

namespace Database\Seeders;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
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
        $account = Account::factory()->count(500)->create();
        $defaultGroup = Group::where('is_default', 1)->first();
        $account->groups()->attach($defaultGroup->getKey());
    }
}
