<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMinecraftAuthCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('minecraft_auth_codes', function (Blueprint $table) {
            $table->bigIncrements('minecraft_auth_code_id');
            $table->uuid('token');
            $table->integer('player_minecraft_id')->unsigned();
            $table->string('uuid', 60)->unique();
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('minecraft_auth_codes');
    }
}
