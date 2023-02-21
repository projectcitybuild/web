<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePlayersMinecraftLastSeenAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->renameColumn('last_seen_at', 'last_synced_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->renameColumn('last_synced_at', 'last_seen_at');
        });
    }
}
