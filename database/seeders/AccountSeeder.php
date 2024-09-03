<?php

namespace Database\Seeders;

use App\Core\Domains\Tokens\TokenGenerator;
use App\Models\Account;
use App\Models\AccountActivation;
use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class AccountSeeder extends Seeder
{
    public function __construct(
        private readonly Google2FA $google2FA,
        private readonly TokenGenerator $tokenGenerator,
    ) {}

    public function run()
    {
        $this->createAdmin();
        $this->createUnactivatedAccount();
        $this->createDummyAccounts();
    }

    private function createAdmin()
    {
        $adminGroup = Group::where('name', 'developer')->first();
        $adminAccount = Account::factory()->make();
        $adminAccount->username = 'Admin';
        $adminAccount->email = 'admin@pcbmc.co';
        $adminAccount->password = Hash::make('test');
        $adminAccount->totp_secret = Crypt::encryptString($this->google2FA->generateSecretKey());
        $adminAccount->totp_backup_code = Crypt::encryptString(Str::random(config('auth.totp.backup_code_length')));
        $adminAccount->is_totp_enabled = true;
        $adminAccount->save();
        $adminAccount->groups()->attach($adminGroup->getKey());
    }

    private function createUnactivatedAccount()
    {
        $account = Account::factory()->make();
        $account->username = 'Unactivated';
        $account->email = 'unactivated@pcbmc.co';
        $account->password = Hash::make('test');
        $account->save();

        AccountActivation::factory()->create([
            'account_id' => $account->getKey(),
            'token' => $this->tokenGenerator->make(),
            'expires_at' => now()->addYear(),
        ]);
    }

    private function createDummyAccounts()
    {
        Account::factory()->count(100)->create();
    }
}
