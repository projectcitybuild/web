<?php

namespace Database\Seeders;

use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Environment;
use App\Entities\Groups\Models\Group;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Players\Models\MinecraftPlayerAlias;
use DB;
use Illuminate\Database\Seeder;
use Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Environment::isProduction()) {
            exit();
        }

        $this->truncateAllTables();

        $this->call(ServerSeeds::class);
        $this->call(GroupSeeds::class);
        $this->call(GameBanSeeder::class);

        $defaultGroup = Group::where('is_default', 1)->first();

        for ($i = 0; $i <= 500; $i++) {
            $account = Account::factory()->create();
            $account->groups()->attach($defaultGroup->getKey());

            $player = MinecraftPlayer::factory()->create(['account_id' => $account->getKey()]);
            MinecraftPlayerAlias::factory()->create(['player_minecraft_id' => $player->getKey()]);

            if (rand(0, 2) === 0) {
                Donation::factory()->create(['account_id' => $account->getKey()]);
            }
        }
    }

    private function truncateAllTables()
    {
        Schema::disableForeignKeyConstraints();

        $tableNames = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        foreach ($tableNames as $name) {
            if ($name === 'migrations') {
                continue;
            }
            DB::table($name)->truncate();
        }

        Schema::enableForeignKeyConstraints();
    }
}
