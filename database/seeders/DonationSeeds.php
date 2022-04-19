<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\DonationTier;
use Illuminate\Database\Seeder;

class DonationSeeds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DonationTier::create([
            'name' => 'copper',
            'currency_reward' => 10,
        ]);
        DonationTier::create([
            'name' => 'iron',
            'currency_reward' => 25,
        ]);
        DonationTier::create([
            'name' => 'diamond',
            'currency_reward' => 50,
        ]);
    }
}
