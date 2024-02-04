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
        Schema::create('badges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name');
            $table->string('unicode_icon');
        });

        Schema::create('badges_pivot', function (Blueprint $table) {
            $table->increments('id');
            $table->string('badge_id');
            $table->string('account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('badges_pivot');
        Schema::dropIfExists('badges');
    }
};
