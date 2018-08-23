<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteServerTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('server_keys', function (Blueprint $table) {
            $table->string('token')->after('server_id');
        });

        Schema::dropIfExists('server_key_tokens');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('server_key_tokens', function (Blueprint $table) {
            $table->increments('server_key_token_id');
            $table->integer('server_key_id')->unsigned();
            $table->char('token_hash', 60)->comment('Token hash for comparison purposes');
            $table->boolean('is_blacklisted')->comment('Whether this token has had access revoked');
            $table->timestamps();

            $table->foreign('server_key_id')->references('server_key_id')->on('server_keys');
        });

        Schema::table('server_keys', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
}
