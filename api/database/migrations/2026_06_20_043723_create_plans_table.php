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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // "Pro", "Enterprise"
            $table->string('slug')->unique();                 // "pro", "enterprise" — used in code, never changes even if name does
            $table->text('description')->nullable();
            $table->unsignedInteger('price');                 // stored in CENTS (2900 = $29.00) — never use float/decimal for money
            $table->string('currency', 3)->default('USD');    // ISO 4217 code
            $table->enum('billing_interval', ['monthly', 'yearly', 'one_time'])->default('monthly');
            $table->json('features')->nullable();             // ["ai_summaries", "pdf_export", "priority_support"]
            $table->boolean('is_active')->default(true);       // soft "is this plan purchasable" toggle, don't delete old plans tenants are still on
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
