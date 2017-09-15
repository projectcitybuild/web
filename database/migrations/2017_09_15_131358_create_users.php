<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // for use later when we switch to discourse
        Schema::create('users', function(Blueprint $table) {
            $table->increments('user_id');
            $table->timestamps();
        });

        Schema::create('game_users', function(Blueprint $table) {
            $table->increments('game_user_id');
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users');
        });

        Schema::create('user_alias_types', function(Blueprint $table) {
            $table->increments('user_alias_type_id');
            $table->string('name');
        });

        Schema::create('user_aliases', function(Blueprint $table) {
            $table->increments('user_alias_id');
            $table->integer('user_alias_type_id')->unsigned();
            $table->integer('game_user_id')->unsigned();
            $table->string('alias');
            $table->timestamps();

            $table->foreign('user_alias_type_id')->references('user_alias_type_id')->on('user_alias_type_id');
            $table->foreign('game_user_id')->references('game_user_id')->on('game_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('user_aliases');
        Schema::dropIfExists('user_alias_types');
        Schema::dropIfExists('game_users');
        Schema::dropIfExists('users');
    }
}
