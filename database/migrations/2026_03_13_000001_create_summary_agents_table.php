<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng summary_agents - lưu prompt tóm tắt tổng hợp
     * Bảng này tách biệt khỏi system_agent vì vai trò khác nhau:
     * - system_agent: agent chat AI (root1-3, trunk1-2)
     * - summary_agents: prompt tóm tắt chạy ngầm khi hoàn thành điều kiện
     */
    public function up(): void
    {
        Schema::create('summary_agents', function (Blueprint $table) {
            $table->id();
            // Tên định danh duy nhất: strategic_platform, authentic_foundation, consistent_identity
            $table->string('name', 100)->unique();
            // Nội dung prompt gửi cho OpenAI
            $table->text('prompt')->nullable();
            // Điều kiện kích hoạt: root_completed, trunk_completed, all_completed
            $table->string('trigger_condition', 50);
            // Nguồn dữ liệu đầu vào: root, trunk, root+trunk
            $table->string('input_source', 50);
            // Bật/tắt prompt
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Xóa bảng summary_agents
     */
    public function down(): void
    {
        Schema::dropIfExists('summary_agents');
    }
};
