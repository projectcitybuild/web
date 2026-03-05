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
        Schema::create('audit_commands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('command');
            $table->string('actor');
            $table->unsignedInteger('player_id')->nullable();
            $table->ipAddress('ip');
            $table->json('meta')->nullable();
            $table->dateTime('created_at');

            $table->foreign('player_id')
                ->references('id')
                ->on('players');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_commands');
    }
};
