<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class AccountFactory extends Factory
{
    public const UNHASHED_PASSWORD = 'secret';

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->email,
            'username' => $this->faker->userName,
            'password' => 'test', // Hashing is expensive
            'activated' => true,
            'last_login_ip' => $this->faker->ipv4,
            'last_login_at' => $this->faker->dateTimeBetween('-180days', '-1hours'),
        ];
    }

    /**
     * Sets the password to the string 'password' unhashed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function passwordHashed()
    {
        return $this->state(function (array $attributes) {
            return [
                'password' => Hash::make(self::UNHASHED_PASSWORD),
            ];
        });
    }

    /**
     * Deactivates the account.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unactivated()
    {
        return $this->state(function (array $attributes) {
            return [
                'activated' => false,
            ];
        });
    }

    public function withTotpCode()
    {
        return $this->state(function (array $attributes) {
            return [
                'totp_secret' => Crypt::encryptString((new Google2FA)->generateSecretKey()),
            ];
        });
    }

    public function withTotpBackupCode()
    {
        return $this->state(function (array $attributes) {
            return [
                'totp_backup_code' => Crypt::encryptString(Str::random(32)),
            ];
        });
    }

    public function hasStartedTotp()
    {
        return $this->withTotpBackupCode()->withTotpCode();
    }

    public function hasFinishedTotp()
    {
        return $this->hasStartedTotp()->state(function (array $attributes) {
            return [
                'is_totp_enabled' => true,
            ];
        });
    }

    public function neverLoggedIn()
    {
        return $this->state(function (array $attributes) {
            return [
                'last_login_at' => null,
            ];
        });
    }
}
