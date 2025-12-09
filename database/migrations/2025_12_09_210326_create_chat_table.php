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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('brand_id');
            $table->bigInteger('agent_id');
            $table->string('title')->nullable();
            $table->integer('total_tokens')->default(0);
            $table->integer('total_messages')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'brand_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chat_id');
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->text('content');

            // OpenAI metadata
            $table->string('model', 100)->nullable();
            $table->integer('prompt_tokens')->nullable();
            $table->integer('completion_tokens')->nullable();
            $table->integer('total_tokens')->nullable();

            // Additional metadata
            $table->json('metadata')->nullable();

            $table->timestamp('created_at');

            $table->index(['chat_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brandtree_tables');
    }
};
