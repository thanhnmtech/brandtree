<?php

// Migration: Thêm cột account_type vào bảng users
// Giá trị: 'student' (Sinh Viên) hoặc 'business' (Doanh Nghiệp)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy migration - thêm cột account_type
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cột loại tài khoản: student hoặc business, mặc định null (chưa chọn)
            $table->string('account_type')->nullable()->after('avatar');
        });
    }

    /**
     * Rollback migration - xóa cột account_type
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account_type');
        });
    }
};
