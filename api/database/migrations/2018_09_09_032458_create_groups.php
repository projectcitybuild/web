<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('group_id');
            $table->string('name');
            $table->string('alias')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_staff')->default(false);
            $table->boolean('is_admin')->default(false);
        });

        Schema::create('groups_accounts', function (Blueprint $table) {
            $table->increments('groups_accounts_id');
            $table->integer('group_id')->unsigned();
            $table->integer('account_id')->unsigned();

            $table->foreign('group_id')->references('group_id')->on('groups');
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
        Schema::dropIfExists('groups_accounts');
        Schema::dropIfExists('groups');
    }
}
