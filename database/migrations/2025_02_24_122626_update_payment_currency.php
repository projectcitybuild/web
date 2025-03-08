<?php

use App\Models\Donation;
use App\Models\Payment;
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
        // https://github.com/laravel/cashier-stripe/blob/15.x/UPGRADE.md#upgrading-to-141211-from-141210
        Schema::table('subscription_items', function (Blueprint $table) {
            $table->dropUnique(['subscription_id', 'stripe_price']);
            $table->index(['subscription_id', 'stripe_price']);
        });

        // https://github.com/laravel/cashier-stripe/blob/15.x/UPGRADE.md#renamed-name-column-to-type
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->renameColumn('name', 'type');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->integer('original_unit_amount')->after('amount_paid_in_cents')->default(0);
            $table->string('original_currency', 3)->after('amount_paid_in_cents')->default("aud");
            $table->integer('paid_unit_amount')->after('amount_paid_in_cents')->default(0);
            $table->string('paid_currency', 3)->after('amount_paid_in_cents')->default("aud");
            $table->renameColumn('quantity', 'unit_quantity');
            $table->dropColumn('is_subscription_payment');
        });

        $payments = Payment::get();
        foreach ($payments as $payment) {
            $payment->original_amount = $payment->amount_paid_in_cents;
            $payment->original_currency = "aud";
            $payment->paid_unit_amount = $payment->amount_paid_in_cents;
            $payment->paid_currency = "aud";
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('amount_paid_in_cents');

            $table->integer('original_unit_amount')->default(null)->change();
            $table->string('original_currency', 3)->default(null)->change();
            $table->integer('paid_unit_amount')->default(null)->change();
            $table->string('paid_currency', 3)->default(null)->change();
        });

        // Convert all current donations to USD (since that's what we've been tracking up until now)
        $donations = Donation::get();
        foreach ($donations as $donation) {
            // We want to now store them in the smallest base unit (cents)
            $donation->amount = number_format($donation->amount * 100);
            $donation->save();
        }

        Schema::table('donations', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id')->nullable()->after('account_id');
            $table->integer('amount')->change();

            $table->foreign('payment_id')
                ->references('payment_id')
                ->on('payments');
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
