<?php

namespace Database\Seeders;

use App\Models\Eloquent\Account;
use App\Models\Eloquent\Donation;
use App\Models\Eloquent\DonationPerk;
use App\Models\Eloquent\DonationTier;
use App\Models\Eloquent\PlayerBan;
use App\Models\Eloquent\Group;
use App\Models\Eloquent\MinecraftPlayer;
use App\Models\Eloquent\Server;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Server::factory()->create([
            'name' => 'Minecraft (Java)',
            'ip' => '158.69.120.168',
            'ip_alias' => 'pcbmc.co',
            'port' => '25565',
            'display_order' => 1,
            'is_online' => true,
            'num_of_players' => 45,
            'num_of_slots' => 100,
        ]);

        Group::factory(count: 10)->create();

        $copperTier = DonationTier::factory()->create([
           'name' => 'copper_tier',
           'currency_reward' => 10,
        ]);
        $ironTier = DonationTier::factory()->create([
            'name' => 'iron_tier',
            'currency_reward' => 15,
        ]);
        $diamondTier = DonationTier::factory()->create([
            'name' => 'diamond_tier',
            'currency_reward' => 20,
        ]);

        $account = Account::factory()->create([
            'username' => 'dev admin',
            'email' => 'admin@pcbmc.co',
            'password' => Hash::make('admin'),
            'email_verified_at' => now(),
        ]);

        $donation = Donation::factory()->create([
            'account_id' => $account->getKey(),
        ]);

        $perk = DonationPerk::factory()->create([
            'account_id' => $account->getKey(),
            'donation_id' => $donation->getKey(),
            'expires_at' => now()->addYears(3),
            'donation_tier_id' => $ironTier->getKey(),
        ]);

        $players = MinecraftPlayer::factory(100)->create();

        for ($x = 0; $x < 100; $x++) {
            PlayerBan::factory()
                ->bannedPlayer($players->random())
                ->create();
        }
    }
}
