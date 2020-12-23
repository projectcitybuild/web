<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeNullablePlayersMinecraftLastSyncedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->dateTime('last_synced_at')->nullable(true)->change();
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
            $table->dateTime('last_synced_at')->nullable(false)->change();
        });
    }
}
