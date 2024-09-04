<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_activations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token');
            $table->integer('account_id')->unsigned();
            $table->timestamps();
            $table->dateTime('expires_at');

            $table->foreign('account_id')
                ->references('account_id')
                ->on('accounts');

            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('account_activations');
    }
};
