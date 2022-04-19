<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminGroup = Group::where('name', 'administrator')->first();
        $adminAccount = Account::factory()->make();
        $adminAccount->username = 'Admin';
        $adminAccount->email = 'admin@pcbmc.co';
        $adminAccount->password = Hash::make('admin');
        $adminAccount->is_totp_enabled = true;
        $adminAccount->save();
        $adminAccount->groups()->attach($adminGroup->getKey());
    }
}
