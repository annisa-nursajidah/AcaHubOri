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
        Schema::create('school_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->unsignedInteger('total_accounts');
            $table->decimal('price_per_account', 12, 2);
            $table->decimal('total_price', 15, 2);
            $table->enum('status', ['pending', 'paid', 'active', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('midtrans_snap_token')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_subscriptions');
    }
};
