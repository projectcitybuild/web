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
        Schema::create('player_mail_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('player_id');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('received_at');
            $table->softDeletes();
            $table->timestamps();

            $table->foreignId('mail_id')
                ->constrained('player_mails')
                ->cascadeOnDelete();

            $table->foreign('player_id')
                ->references('id')
                ->on('players')
                ->cascadeOnDelete();

            $table->unique(
                ['mail_id', 'player_id'],
                'player_mail_recipients_mail_player_unique',
            );

            $table->index(
                ['player_id', 'deleted_at', 'received_at', 'mail_id'],
                'player_mail_recipients_inbox_idx',
            );

            $table->index(
                ['player_id', 'deleted_at', 'read_at'],
                'player_mail_recipients_unread_idx',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_mail_recipients');
    }
};
