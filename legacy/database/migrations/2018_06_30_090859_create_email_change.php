<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_email_changes', function (Blueprint $table) {
            $table->increments('account_email_change_id');
            $table->integer('account_id')->unsigned();
            $table->string('token');
            $table->string('email_previous');
            $table->string('email_new');
            $table->boolean('is_previous_confirmed');
            $table->boolean('is_new_confirmed');
            $table->timestamps();

            $table->index(['token', 'email_previous', 'email_new']);
            $table->foreign('account_id')->references('account_id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_email_changes');
    }
}
