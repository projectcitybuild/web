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
        Schema::create('builder_rank_applications', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->unsigned();
            $table->string('minecraft_alias');
            $table->string('current_builder_rank');
            $table->string('build_location');
            $table->text('build_description');
            $table->text('additional_notes')->nullable();
            $table->integer('status');
            $table->text('denied_reason')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('builder_rank_applications');
    }
};
