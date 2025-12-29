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
        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->integer('sessions')->default(0)->after('nickname');
            $table->integer('afk_time')->default(0)->after('nickname');
            $table->integer('play_time')->default(0)->after('nickname');
            $table->integer('blocks_placed')->default(0)->after('nickname');
            $table->integer('blocks_destroyed')->default(0)->after('nickname');
            $table->integer('blocks_travelled')->default(0)->after('nickname');
            $table->float('fly_speed')->default(1)->after('nickname');
            $table->float('walk_speed')->default(1)->after('nickname');
            $table->boolean('muted')->default(false)->after('nickname');
        });

        Schema::create('minecraft_player_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('player_id');
            $table->integer('seconds');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');

            $table->index(['starts_at', 'ends_at']);

            $table->foreign('player_id')
                ->references('player_minecraft_id')
                ->on('players_minecraft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minecraft_player_sessions');

        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->dropColumn('sessions');
            $table->dropColumn('afk_time');
            $table->dropColumn('play_time');
            $table->dropColumn('blocks_placed');
            $table->dropColumn('blocks_destroyed');
            $table->dropColumn('blocks_travelled');
            $table->dropColumn('fly_speed');
            $table->dropColumn('walk_speed');
            $table->dropColumn('muted');
        });
    }
};
