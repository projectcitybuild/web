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
        Schema::create('player_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('reporter_name');
            $table->string('reporter_contact');
            $table->string('reporter_email')->nullable();
            $table->string('reported_player_name');
            $table->string('subject', 150);
            $table->text('details');
            $table->string('visibility', 24)->default('standard');
            $table->string('status', 24)->default('open');
            $table->text('completion_message')->nullable();
            $table->foreignId('resolved_by_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['visibility', 'status', 'created_at']);
        });

        Schema::create('player_report_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('assigned_by_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['player_report_id', 'account_id']);
        });

        Schema::create('player_report_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_report_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 127);
            $table->unsignedBigInteger('file_size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_report_attachments');
        Schema::dropIfExists('player_report_assignments');
        Schema::dropIfExists('player_reports');
    }
};
