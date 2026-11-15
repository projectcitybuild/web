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
        Schema::create('player_mail_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('status', 20)->default('queued');
            $table->unsignedInteger('max_player_id')->default(0);
            $table->unsignedInteger('last_player_id')->default(0);
            $table->unsignedBigInteger('recipients_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreignId('mail_id')
                ->unique()
                ->constrained('player_mails')
                ->cascadeOnDelete();

            $table->index(
                ['status', 'updated_at'],
                'player_mail_broadcasts_recovery_idx',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_mail_broadcasts');
    }
};
