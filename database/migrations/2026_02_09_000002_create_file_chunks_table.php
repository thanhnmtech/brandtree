<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Bảng lưu chunks và embedding vectors cho RAG search
     * Yêu cầu MariaDB 11.7+ với VECTOR support
     */
    public function up(): void
    {
        Schema::create('file_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_file_id')->constrained('uploaded_files')->onDelete('cascade');
            $table->unsignedInteger('chunk_index');
            $table->longText('chunk_text');
            $table->timestamps();

            $table->index(['uploaded_file_id', 'chunk_index']);
        });

        // Thêm column VECTOR cho embedding (MariaDB 11.7+)
        // Gemini text-embedding-004 có 768 dimensions
        DB::statement('ALTER TABLE file_chunks ADD COLUMN embedding VECTOR(768) NOT NULL');

        // Thêm HNSW index cho vector search với cosine distance
        DB::statement('ALTER TABLE file_chunks ADD VECTOR INDEX embedding_idx (embedding) M=16 DISTANCE=COSINE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_chunks');
    }
};
