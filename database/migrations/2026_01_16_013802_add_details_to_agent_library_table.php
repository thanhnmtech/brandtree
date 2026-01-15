<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_library', function (Blueprint $table) {
            // Đã bỏ cột prompt
            
            // Thêm cột agent_key (đặt sau instruction để liền mạch)
            $table->string('agent_key')->nullable()->after('instruction');
            
            // Thêm cột vector_id
            $table->string('vector_id')->nullable()->after('agent_key');
        });
    }

    public function down(): void
    {
        Schema::table('agent_library', function (Blueprint $table) {
            // Chỉ xóa 2 cột này khi rollback
            $table->dropColumn(['agent_key', 'vector_id']);
        });
    }
};