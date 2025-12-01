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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('brand_subscriptions')->onDelete('set null');
            $table->unsignedBigInteger('amount'); // Amount in VND
            $table->string('payment_method')->default('sepay'); // sepay, bank_transfer, etc.
            $table->string('transaction_id')->nullable()->unique();
            $table->string('sepay_reference')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable(); // Store additional Sepay response data
            $table->timestamps();

            $table->index(['brand_id', 'status']);
            $table->index('transaction_id');
            $table->index('sepay_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
