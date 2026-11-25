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
        Schema::create('minecraft_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('player_id')->constrained('players');
            $table->uuid('event_id')->unique();
            $table->string('service_name');
            $table->string('address');
            $table->unsignedBigInteger('source_timestamp');
            $table->dateTime('voted_at');
            $table->longText('additional_data')->nullable();
            $table->timestamps();

            $table->index(['server_id', 'voted_at']);
            $table->index(['player_id', 'voted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minecraft_votes');
    }
};
