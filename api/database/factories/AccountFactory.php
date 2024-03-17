<?php

namespace Database\Factories;

use App\Models\Eloquent\Account;
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
}
