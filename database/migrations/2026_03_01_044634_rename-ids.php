<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->renameColumn(from: 'account_id', to: 'id');
        });
        Schema::table('donation_perks', function (Blueprint $table) {
            $table->renameColumn(from: 'donation_perks_id', to: 'id');
        });
        Schema::table('donation_tiers', function (Blueprint $table) {
            $table->renameColumn(from: 'donation_tier_id', to: 'id');
        });
        Schema::table('donations', function (Blueprint $table) {
            $table->renameColumn(from: 'donation_id', to: 'id');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn(from: 'payment_id', to: 'id');
        });
        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->renameColumn(from: 'player_minecraft_id', to: 'id');
        });
        Schema::table('servers', function (Blueprint $table) {
            $table->renameColumn(from: 'server_id', to: 'id');
        });

        Schema::rename(from: 'players_minecraft', to: 'players');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename(from: 'players', to: 'players_minecraft');

        Schema::table('accounts', function (Blueprint $table) {
            $table->renameColumn(from: 'id', to: 'account_id');
        });
        Schema::table('donation_perks', function (Blueprint $table) {
            $table->renameColumn(from: 'id', to: 'donation_perks_id');
        });
        Schema::table('donation_tiers', function (Blueprint $table) {
            $table->renameColumn(from: 'id', to: 'donation_tier_id');
        });
        Schema::table('donations', function (Blueprint $table) {
            $table->renameColumn(from: 'id', to: 'donation_id');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn(from: 'id', to: 'payment_id');
        });
        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->renameColumn(from: 'id', to: 'player_minecraft_id');
        });
        Schema::table('servers', function (Blueprint $table) {
            $table->renameColumn(from: 'id', to: 'server_id');
        });
    }
};
