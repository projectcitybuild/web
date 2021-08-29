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
        $this->call(ServerSeeds::class);
        $this->call(GroupSeeds::class);
        $this->call(AccountSeeder::class);
        $this->call(GameBanSeeder::class);
        $this->call(DonationSeeds::class);
    }
}
