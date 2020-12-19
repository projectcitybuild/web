<?php

namespace Database\Seeders;

use App\Entities\Accounts\Models\Account;
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
        $this->call(GameBanSeeder::class);
    }
}
