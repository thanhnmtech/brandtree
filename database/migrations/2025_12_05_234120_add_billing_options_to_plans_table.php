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
        Schema::table('plans', function (Blueprint $table) {
            // Plan type: subscription (gói đăng ký) | credit_package (nạp credit)
            $table->enum('type', ['subscription', 'credit_package'])
                  ->default('subscription')
                  ->after('slug');

            // Chu kỳ thanh toán (chỉ áp dụng cho subscription)
            $table->enum('billing_cycle', ['monthly', 'yearly'])
                  ->nullable()
                  ->after('duration_days');

            // Giá gốc (để hiện giá gạch ngang khi giảm giá)
            $table->unsignedBigInteger('original_price')
                  ->nullable()
                  ->after('price');

            // Link đến plan gốc (cho gói năm link về gói tháng)
            $table->foreignId('parent_plan_id')
                  ->nullable()
                  ->after('billing_cycle')
                  ->constrained('plans')
                  ->nullOnDelete();

            // Index for filtering
            $table->index('type');
            $table->index('billing_cycle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropForeign(['parent_plan_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['billing_cycle']);
            $table->dropColumn(['type', 'billing_cycle', 'original_price', 'parent_plan_id']);
        });
    }
};
