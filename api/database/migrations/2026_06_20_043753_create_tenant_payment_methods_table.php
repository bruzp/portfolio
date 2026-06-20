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
        Schema::create('tenant_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();

            // The token/ID the PROVIDER gives you to charge this tenant later.
            // NEVER store actual card numbers — this is a reference token only (e.g. Stripe's "pm_xyz").
            $table->string('provider_reference_id');

            $table->string('provider_customer_id')->nullable(); // Stripe "cus_xyz" / PayPal payer ID — links tenant to provider's customer object
            $table->string('display_label')->nullable();         // "Visa •••• 4242" — safe to show in UI, no real card data
            $table->boolean('is_default')->default(false);
            $table->timestamp('expires_at')->nullable();          // card expiration, useful for proactive "update your card" prompts
            $table->timestamps();

            $table->unique(['tenant_id', 'provider_reference_id']); // prevent duplicate saves of the same token
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_payment_methods');
    }
};
