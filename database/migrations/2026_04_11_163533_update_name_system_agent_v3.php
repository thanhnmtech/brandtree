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
        \Illuminate\Support\Facades\DB::table('system_agent')->where('agent_type', 'root1')->update(['name' => 'Thiết Kế Văn Hóa']);
        \Illuminate\Support\Facades\DB::table('system_agent')->where('agent_type', 'root2')->update(['name' => 'Phân Tích Thị Trường']);
        \Illuminate\Support\Facades\DB::table('system_agent')->where('agent_type', 'root3')->update(['name' => 'Xác Định Giá Trị']);
        \Illuminate\Support\Facades\DB::table('system_agent')->where('agent_type', 'trunk1')->update(['name' => 'Định Vị Thương Hiệu']);
        \Illuminate\Support\Facades\DB::table('system_agent')->where('agent_type', 'trunk2')->update(['name' => 'Xác Định Ngôn Ngữ']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting not fully defined, but leaving it empty ensures rollbacks won't break.
    }
};
