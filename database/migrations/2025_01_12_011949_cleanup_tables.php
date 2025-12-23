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
        Schema::drop('players_minecraft_aliases');
        Schema::drop('account_balance_transactions');

        Schema::table('game_player_bans', function (Blueprint $table) {
            $table->dropColumn('server_id');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('balance');
        });

        Schema::table('donation_perks', function (Blueprint $table) {
            $table->dropColumn('is_lifetime_perks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
