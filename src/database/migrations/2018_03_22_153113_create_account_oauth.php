<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountOauth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('account_links', function(Blueprint $table) {
            $table->increments('account_link_id');
            $table->integer('account_id');
            $table->string('provider_name')->nullable();
            $table->string('provider_id')->unique()->nullable();
            $table->timestamps();
        });

        Schema::table('accounts', function(Blueprint $table) {
            $table->string('remember_token', 60)->nullable()->change();
            $table->string('last_login_ip', 45)->nullable()->change();
            $table->datetime('last_login_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('accounts', function(Blueprint $table) {
            $table->string('remember_token', 60)->change();
            $table->string('last_login_ip', 45)->change();
            $table->datetime('last_login_at')->change();
        });

        Schema::dropIfExists('account_links');
    }
}
