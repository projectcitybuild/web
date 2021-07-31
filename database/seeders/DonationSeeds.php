<?php

namespace Database\Seeders;

use App\Entities\Donations\Models\Donation;
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
            'min_donation_amount' => 3,
        ]);
        DonationTier::create([
            'name' => 'iron',
            'min_donation_amount' => 5,
        ]);
        DonationTier::create([
            'name' => 'gold',
            'min_donation_amount' => 10,
        ]);
        DonationTier::create([
            'name' => 'diamond',
            'min_donation_amount' => 15,
        ]);
        DonationTier::create([
            'name' => 'netherite',
            'min_donation_amount' => 25,
        ]);
    }
}
