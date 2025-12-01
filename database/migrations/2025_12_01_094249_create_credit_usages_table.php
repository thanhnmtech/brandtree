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
        Schema::create('credit_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('brand_subscriptions')->onDelete('set null');
            $table->integer('amount'); // Positive for deduction, negative for addition
            $table->string('model_used')->nullable(); // GPT-4o mini, GPT-5, etc.
            $table->string('action_type'); // chat, image_generation, etc.
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['brand_id', 'created_at']);
            $table->index('user_id');
            $table->index('action_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_usages');
    }
};
