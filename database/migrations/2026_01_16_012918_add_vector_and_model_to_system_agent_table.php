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
        Schema::table('system_agent', function (Blueprint $table) {
            // Thêm 2 cột mới, cho phép null để không lỗi dữ liệu cũ
            $table->string('vector_id')->nullable()->after('name'); 
            $table->string('model')->nullable()->after('vector_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_agent', function (Blueprint $table) {
            $table->dropColumn(['vector_id', 'model']);
        });
    }
};