<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột summary_data vào bảng brands
     * Lưu kết quả tóm tắt tổng hợp dưới dạng JSON:
     * {
     *   "strategic_platform": "...",
     *   "authentic_foundation": "...",
     *   "consistent_identity": "..."
     * }
     */
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->json('summary_data')->nullable()->after('trunk_brief_data');
        });
    }

    /**
     * Xóa cột summary_data
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('summary_data');
        });
    }
};
