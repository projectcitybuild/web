<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeletePlayersMinecraftPlaytime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->dropColumn('playtime');
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
            $table->integer('playtime')->unsigned()->comment('Total playtime in minutes');
        });
    }
}
