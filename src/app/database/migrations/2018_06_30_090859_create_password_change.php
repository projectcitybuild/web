<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswordChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('account_email_changes', function(Blueprint $table) {
            $table->increments('account_email_change_id');
            $table->integer('account_id');
            $table->string('token');
            $table->string('email_previous');
            $table->string('email_new');
            $table->boolean('is_previous_confirmed');
            $table->boolean('is_new_confirmed');
            $table->boolean('is_complete');
            $table->timestamps();

            $table->foreign('account_id')->references('account_id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('account_email_changes');
    }
}
