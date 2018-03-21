<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRegisterEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('account_activation_codes', function(Blueprint $table) {
            $table->increments('account_activation_id');
            $table->string('token');
            $table->string('email');
            $table->string('password');
            $table->boolean('is_used')->comment('Whether the code has been used already');
            $table->datetime('expires_at')->comment('The datetime when this record will be purged');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('account_activation_codes');
    }
}
