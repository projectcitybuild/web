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
        Schema::create('player_mails', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sender_player_id')->nullable();
            $table->string('sender_name');
            $table->unsignedInteger('created_by_account_id')->nullable();
            $table->string('category', 20);
            $table->string('subject', 191);
            $table->mediumText('body');
            $table->timestamps();

            $table->foreign('sender_player_id')
                ->references('id')
                ->on('players')
                ->nullOnDelete();

            $table->foreign('created_by_account_id')
                ->references('id')
                ->on('accounts')
                ->nullOnDelete();

            $table->index(
                ['category', 'created_at'],
                'player_mails_category_created_idx',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_mails');
    }
};
