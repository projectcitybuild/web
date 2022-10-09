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
        Schema::create('showcase_applications', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->unsigned();
            $table->string('name');
            $table->string('title');
            $table->text('description');
            $table->string('creators');
            $table->string('location_world');
            $table->double('location_x');
            $table->double('location_y');
            $table->double('location_z');
            $table->float('location_pitch');
            $table->float('location_yaw');
            $table->dateTime('built_at')->nullable();
            $table->tinyInteger('status')->unsigned();
            $table->text('decision_note')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->integer('decider_account_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('account_id')->on('accounts');
            $table->foreign('decider_account_id')->references('account_id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('showcase_applications');
    }
};
