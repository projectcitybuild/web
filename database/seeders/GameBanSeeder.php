<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GameBanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Entities\Bans\Models\GameBan::class, 30)->create();
    }
}
