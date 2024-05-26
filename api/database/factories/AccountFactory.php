<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

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
            'email_verified_at' => $this->faker->dateTime(),
        ];
    }

    /**
     * Sets the password to the string 'password' unhashed.
     *
     * @return Factory
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
     * @return Factory
     */
    public function unactivated()
    {
        return $this->state(function (array $attributes) {
            return [
                'activated' => false,
            ];
        });
    }

    /**
     * Account has enabled 2FA (but hasn't verified it)
     *
     * @return Factory
     */
    public function enabled2FA()
    {
        return $this->state(function (array $attributes) {
            return [
                'two_factor_secret' => 'secret',
            ];
        });
    }

    /**
     * Account has enabled 2FA (but hasn't verified it)
     *
     * @return Factory
     */
    public function verified2FA()
    {
        return $this->state(function (array $attributes) {
            return [
                'two_factor_secret' => 'secret',
                'two_factor_recovery_codes' => 'codes',
                'two_factor_confirmed_at' => $this->faker->dateTime(),
            ];
        });
    }
}
