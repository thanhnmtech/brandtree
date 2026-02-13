<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Bảng lưu thông tin file đã upload cho RAG
     */
    public function up(): void
    {
        Schema::create('uploaded_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('cascade');

            // Polymorphic: attachable to Chat or BrandAgent
            $table->morphs('attachable'); // attachable_type, attachable_id

            $table->string('filename');           // Tên file gốc
            $table->string('file_path');          // Path lưu trữ
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // bytes
            $table->longText('original_content')->nullable(); // Text extracted
            $table->longText('image_description')->nullable(); // Mô tả từ Gemini Vision

            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();

            $table->timestamps();

            // morphs() đã tự tạo index cho attachable_type, attachable_id
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaded_files');
    }
};
