<?php

use Entities\Models\Eloquent\GameUnban;
use Illuminate\Database\Migrations\Migration;
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
        $unbans = GameUnban::all();

        foreach ($unbans as $unban) {
            $ban = $unban->ban;
            $ban->unbanned_at = $unban->created_at;
            $ban->unbanner_player_id = $unban->staff_player_id;
            $ban->save();
        }

        Schema::drop('game_network_unbans');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('game_network_unbans', function (Blueprint $table) {
            $table->increments('game_unban_id');
            $table->integer('game_ban_id')->unsigned();
            $table->integer('staff_player_id')->unsigned();
            $table->string('staff_player_type');
            $table->timestamps();

            $table->foreign('game_ban_id')->references('game_ban_id')->on('game_network_bans');
        });
    }
};
