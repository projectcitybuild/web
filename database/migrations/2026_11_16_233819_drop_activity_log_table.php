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
        Schema::dropIfExists('activity_log');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name', 191)->nullable();
            $table->text('description');
            $table->string('subject_type', 191)->nullable();
            $table->string('event', 191)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('causer_type', 191)->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id'], 'subject');
            $table->index(['causer_type', 'causer_id'], 'causer');
            $table->index('log_name', 'activity_log_log_name_index');
        });
    }
};
