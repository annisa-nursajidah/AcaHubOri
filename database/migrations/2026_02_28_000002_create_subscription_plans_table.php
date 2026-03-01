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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // e.g. "Starter", "Professional", "Enterprise"
            $table->text('description')->nullable();
            $table->decimal('price_per_account', 12, 2);     // harga per akun
            $table->unsignedInteger('min_accounts')->default(10);
            $table->unsignedInteger('max_accounts')->nullable(); // null = unlimited
            $table->json('features')->nullable();            // fitur yang termasuk
            $table->unsignedInteger('duration_days')->default(365); // masa aktif (hari)
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
