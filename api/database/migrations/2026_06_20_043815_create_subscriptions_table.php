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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained();
            $table->foreignId('tenant_payment_method_id')->nullable()->constrained()->nullOnDelete();

            $table->string('provider');                     // "stripe", "paypal" — duplicated here intentionally, see note below
            $table->string('provider_subscription_id')->nullable(); // Stripe "sub_xyz" / PayPal subscription ID

            $table->enum('status', [
                'trialing', 'active', 'past_due', 'canceled', 'incomplete', 'expired',
            ])->default('incomplete');

            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_starts_at')->nullable();
            $table->timestamp('current_period_ends_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']); // fast lookup: "give me this tenant's active subscription"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
