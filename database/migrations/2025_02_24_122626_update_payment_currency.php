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
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('original_amount')->after('amount_paid_in_cents');
            $table->string('original_currency', 3)->after('amount_paid_in_cents');
            $table->integer('paid_amount')->after('amount_paid_in_cents');
            $table->string('paid_currency', 3)->after('amount_paid_in_cents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
