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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');                  // "Credit/Debit Card", "PayPal"
            $table->string('slug')->unique();         // "stripe_card", "paypal" — referenced in code
            $table->string('provider');               // "stripe", "paypal" — which SDK/API integration handles this
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0); // controls display order on checkout UI
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
