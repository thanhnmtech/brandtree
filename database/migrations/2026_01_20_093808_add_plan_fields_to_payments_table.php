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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('subscription_id')->constrained('plans')->onDelete('set null');
            $table->string('billing_cycle')->nullable()->after('plan_id'); // monthly, yearly
            $table->string('payment_type')->default('new')->after('billing_cycle'); // new, renewal
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn(['plan_id', 'billing_cycle', 'payment_type']);
        });
    }
};
