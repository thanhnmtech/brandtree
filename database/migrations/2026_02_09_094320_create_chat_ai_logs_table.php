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
        Schema::create('chat_ai_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('chat_id')->nullable()->constrained('chats')->nullOnDelete();

            // Brand info
            $table->foreignId('brand_id')->nullable();
            $table->string('brand_name')->nullable();

            // Agent info (ID is string because it could be 'root1' or '24')
            $table->string('agent_id')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('agent_type')->nullable(); // system, canopy, etc.

            // Request details
            $table->string('model')->nullable();
            $table->longText('final_user_content')->nullable(); // Final prompt user sent (with RAG chunks)
            $table->longText('final_system_instruction')->nullable(); // Final system instruction

            // Token usage (optional for future)
            $table->integer('input_tokens')->nullable();
            $table->integer('output_tokens')->nullable();

            $table->timestamps();

            // Indexes for faster lookup
            $table->index(['created_at', 'brand_id']);
            $table->index('chat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_ai_logs');
    }
};
