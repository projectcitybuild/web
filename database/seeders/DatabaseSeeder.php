<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ServerSeeder::class);
        $this->call(GroupSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(GameBanSeeder::class);
        $this->call(DonationSeeder::class);
        $this->call(BadgeSeeder::class);
        $this->call(ServerTokenScopeSeeder::class);
    }
}
