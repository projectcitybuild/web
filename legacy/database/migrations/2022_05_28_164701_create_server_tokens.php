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
        Schema::create('server_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token')->index();
            $table->integer('server_id')->unsigned();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('server_id')->references('server_id')->on('servers');
        });

        Schema::create('server_token_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('scope')->index();
        });

        Schema::create('server_token_scopes_pivot', function (Blueprint $table) {
            $table->id();
            $table->integer('token_id')->unsigned();
            $table->integer('scope_id')->unsigned();

            $table->foreign('token_id')->references('id')->on('server_tokens');
            $table->foreign('scope_id')->references('id')->on('server_token_scopes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_tokens');
    }
};
