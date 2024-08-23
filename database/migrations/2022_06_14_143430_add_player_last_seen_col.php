<?php

use App\Models\MinecraftPlayer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable()->after('last_synced_at');
        });

        MinecraftPlayer::get()->each(function ($player) {
            $player->last_seen_at = $player->last_synced_at;
            $player->save();
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
            $table->dropColumn('last_seen_at');
        });
    }
};
