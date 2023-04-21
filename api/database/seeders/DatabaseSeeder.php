<?php

namespace Database\Seeders;

use App\Models\Eloquent\Account;
use App\Models\Eloquent\Group;
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

        Group::factory(10)->create();

        Account::factory(1)->create([
            'email' => 'admin@pcbmc.co',
            'password' => Hash::make('admin'),
            'email_verified_at' => null,
        ]);
    }
}
