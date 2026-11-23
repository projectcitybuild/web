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
        Schema::create('background_job_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('queue_job_id')->nullable();
            $table->string('job_name');
            $table->string('job_type', 40);
            $table->string('status', 20)->default('queued');
            $table->string('queue')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedInteger('max_attempts')->nullable();
            $table->dateTime('queued_at', 6)->nullable();
            $table->dateTime('started_at', 6)->nullable();
            $table->dateTime('completed_at', 6)->nullable();
            $table->dateTime('failed_at', 6)->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('exception_class')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps(6);

            $table->index(['completed_at', 'status']);
            $table->index(['job_type', 'completed_at']);
            $table->index(['status', 'queued_at']);
            $table->index('queue');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_job_runs');
    }
};
