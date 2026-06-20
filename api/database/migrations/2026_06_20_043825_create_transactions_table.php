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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete(); // null = one-time purchase, not tied to a subscription
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();         // what was purchased (subscription renewal OR one-time plan/addon)
            $table->foreignId('tenant_payment_method_id')->nullable()->constrained()->nullOnDelete();

            $table->string('provider');                       // "stripe", "paypal"
            $table->string('provider_transaction_id')->unique(); // Stripe charge/PaymentIntent ID, PayPal transaction ID — used to reconcile webhooks

            $table->enum('type', ['subscription_charge', 'one_time', 'refund']);

            $table->unsignedInteger('amount');                 // total charged, in cents
            $table->unsignedInteger('fee_amount')->default(0);  // what the PROVIDER charged you (their cut), in cents — snapshotted at time of transaction
            $table->string('currency', 3)->default('USD');

            $table->enum('status', ['pending', 'succeeded', 'failed', 'refunded']);
            $table->text('failure_reason')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'created_at']); // fast lookup: tenant's transaction history, sorted by date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
