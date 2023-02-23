<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        Server::factory(5)->create();

        Group::factory(10)->create();

        Account::factory(1)->create([
            'email' => 'admin@pcbmc.co',
            'password' => Hash::make('admin'),
            'email_verified_at' => null,
        ]);
    }
}
