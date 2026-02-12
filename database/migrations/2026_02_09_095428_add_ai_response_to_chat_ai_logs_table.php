<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chat_ai_logs', function (Blueprint $table) {
            $table->longText('ai_response')->nullable()->after('final_system_instruction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_ai_logs', function (Blueprint $table) {
            //
        });
    }
};
