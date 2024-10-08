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
        Schema::create('minecraft_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('minecraft_uuid');
            $table->string('minecraft_alias');
            $table->string('code', length: 6);
            $table->timestamps();
            $table->dateTime('expires_at');
        });

        Schema::drop('minecraft_auth_codes');
        Schema::drop('server_token_scopes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Point of no return
    }
};
