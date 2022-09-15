<?php

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
        Schema::create('game_ip_bans', function (Blueprint $table) {
            $table->id();
            $table->integer('banner_player_id')->unsigned();
            $table->ipAddress();
            $table->string('reason');
            $table->timestamps();
        });
        Schema::rename(from: 'game_network_bans', to: 'game_player_bans');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_ip_bans');
        Schema::rename(from: 'game_player_bans', to: 'game_network_bans');
    }
};
