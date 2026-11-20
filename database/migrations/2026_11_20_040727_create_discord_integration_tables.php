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
        Schema::create('external_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->string('provider', 32);
            $table->string('provider_user_id', 20);
            $table->string('username', 100)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_user_id']);
            $table->unique(['account_id', 'provider']);
        });

        Schema::create('discord_role_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id', 20);
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete();
            $table->string('discord_role_id', 20);
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique(['guild_id', 'role_id']);
            $table->unique(['guild_id', 'discord_role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discord_role_mappings');
        Schema::dropIfExists('external_accounts');
    }
};
