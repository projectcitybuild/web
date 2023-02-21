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
        Schema::drop('server_keys');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('server_keys', function (Blueprint $table) {
            $table->increments('server_key_id');
            $table->integer('server_id')->unsigned();
            $table->string('token');
            $table->boolean('can_local_ban')->default(true);
            $table->boolean('can_global_ban')->default(false);
            $table->boolean('can_warn')->default(true);
            $table->timestamps();

            $table->foreign('server_id')->references('server_id')->on('servers');
        });
    }
};
