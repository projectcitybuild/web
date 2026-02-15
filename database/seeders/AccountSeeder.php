<?php

namespace Database\Seeders;

use App\Core\Utilities\SecureTokenGenerator;
use App\Models\Account;
use App\Models\AccountActivation;
use App\Models\EmailChange;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class AccountSeeder extends Seeder
{
    public function __construct(
        private readonly Google2FA $google2FA,
        private readonly SecureTokenGenerator $tokenGenerator,
    ) {}

    public function run()
    {
        $this->createAdmin();
        $this->createModerator();
        $this->createArchitect();
        $this->createMember();
        $this->createUnactivatedAccount();
        $this->createAccountWithEmailChanges();
        $this->createDummyAccounts();
    }

    private function createAdmin()
    {
        $role = Role::where('name', 'developer')->first();

        $account = Account::factory()->make();
        $account->username = 'Admin';
        $account->email = 'admin@pcbmc.co';
        $account->password = Hash::make('test');
        $account->totp_secret = Crypt::encryptString($this->google2FA->generateSecretKey());
        $account->totp_backup_code = Crypt::encryptString(Str::random(config('auth.totp.backup_code_length')));
        $account->is_totp_enabled = true;
        $account->save();
        $account->roles()->attach($role->id);
    }

    private function createArchitect()
    {
        $role = Role::where('name', 'architect')->first();

        $account = Account::factory()->make();
        $account->username = 'Architect';
        $account->email = 'architect@pcbmc.co';
        $account->password = Hash::make('test');
        $account->totp_secret = Crypt::encryptString($this->google2FA->generateSecretKey());
        $account->totp_backup_code = Crypt::encryptString(Str::random(config('auth.totp.backup_code_length')));
        $account->is_totp_enabled = true;
        $account->save();
        $account->roles()->attach($role->id);
    }

    private function createModerator()
    {
        $role = Role::where('name', 'moderator')->first();

        $account = Account::factory()->make();
        $account->username = 'Moderator';
        $account->email = 'moderator@pcbmc.co';
        $account->password = Hash::make('test');
        $account->totp_secret = Crypt::encryptString($this->google2FA->generateSecretKey());
        $account->totp_backup_code = Crypt::encryptString(Str::random(config('auth.totp.backup_code_length')));
        $account->is_totp_enabled = true;
        $account->save();
        $account->roles()->attach($role->id);
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

    private function createMember()
    {
        $account = Account::factory()->make();
        $account->username = 'Member';
        $account->email = 'member@pcbmc.co';
        $account->password = Hash::make('test');
        $account->save();
    }

    private function createAccountWithEmailChanges()
    {
        $account = Account::factory()->make();
        $account->username = 'Bob';
        $account->email = 'email_changes@pcbmc.co';
        $account->password = Hash::make('test');
        $account->save();

        EmailChange::factory()
            ->for($account)
            ->count(10)
            ->create();
    }

    private function createDummyAccounts()
    {
        Account::factory()
            ->passwordHashed('password')
            ->count(50)
            ->create();
    }
}
