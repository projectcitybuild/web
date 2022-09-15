<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class AccountSeeder extends Seeder
{
    public function __construct(
        private Google2FA $google2FA,
    ) {
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminGroup = Group::where('name', 'developer')->first();
        $adminAccount = Account::factory()->make();
        $adminAccount->username = 'Admin';
        $adminAccount->email = 'admin@pcbmc.co';
        $adminAccount->password = Hash::make('admin');
        $adminAccount->totp_secret = Crypt::encryptString($this->google2FA->generateSecretKey());
        $adminAccount->totp_backup_code = Crypt::encryptString(Str::random(config('auth.totp.backup_code_length')));
        $adminAccount->is_totp_enabled = true;
        $adminAccount->save();
        $adminAccount->groups()->attach($adminGroup->getKey());

        Account::factory()->count(100)->create();
    }
}
