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
        ]);
        DonationTier::create([
            'name' => 'iron',
        ]);
        DonationTier::create([
            'name' => 'diamond',
        ]);
    }
}
