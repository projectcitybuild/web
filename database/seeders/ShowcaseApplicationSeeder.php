<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\ShowcaseApplication;
use Illuminate\Database\Seeder;

class ShowcaseApplicationSeeder extends Seeder
{
    public function run()
    {
        $accounts = Account::get();

        for ($i = 0; $i < 20; $i++) {
            $app = ShowcaseApplication::factory()
                ->for($accounts->random());

            switch (rand(0, 2)) {
                case 1:
                    $app = $app->approved();
                    break;
                case 2:
                    $app = $app->denied();
                    break;
            }

            $app->create();
        }
    }
}
