<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\DonationTier;
use Entities\Models\Eloquent\ShowcaseWarp;
use Entities\Models\Eloquent\StripeProduct;
use Illuminate\Database\Seeder;

class ShowcaseWarpSeeder extends Seeder
{
    public function run()
    {
        ShowcaseWarp::factory()
            ->count(50)
            ->create();
    }
}
