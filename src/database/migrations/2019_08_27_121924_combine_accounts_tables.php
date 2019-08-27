<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CombineAccountsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('accounts_unactivated');

        // Create the column with a default value of 1 for existing accounts
        Schema::table('accounts', function (Blueprint $table) {
            $table->boolean('activated')->default(1);
        });

        // Alter the default to 0 for new accounts
        Schema::table('accounts', function (Blueprint $table) {
            $table->boolean('activated')->default(0)->change();
        });
    }

    // No drop method because this isn't really reversible
}
