<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột brief_prompt vào bảng system_agent
     */
    public function up(): void
    {
        Schema::table('system_agent', function (Blueprint $table) {
            // Cột lưu prompt dùng để gọi OpenAI tóm tắt nội dung phân tích
            $table->text('brief_prompt')->nullable()->after('prompt');
        });
    }

    /**
     * Xóa cột brief_prompt
     */
    public function down(): void
    {
        Schema::table('system_agent', function (Blueprint $table) {
            $table->dropColumn('brief_prompt');
        });
    }
};
