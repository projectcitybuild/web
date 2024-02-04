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
        Schema::create('group_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('scope')->index();
        });

        Schema::create('group_scopes_pivot', function (Blueprint $table) {
            $table->id();
            $table->integer('group_id')->unsigned();
            $table->integer('scope_id')->unsigned();

            $table->foreign('group_id')
                ->references('group_id')
                ->on('groups')
                ->onDelete('cascade');

            $table->foreign('scope_id')
                ->references('id')
                ->on('group_scopes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_scopes_pivot');
        Schema::dropIfExists('group_scopes');
    }
};
