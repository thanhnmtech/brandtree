<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Tạo bảng system_prompts để quản lý các prompt hệ thống
     */
    public function up(): void
    {
        Schema::create('system_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('short_code', 50)->unique()->comment('Mã ngắn gọn để truy vấn prompt');
            $table->text('prompt')->comment('Nội dung prompt');
            $table->text('description')->nullable()->comment('Mô tả mục đích sử dụng');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_prompts');
    }
};
