<?php

namespace App\Jobs;

use App\Models\UploadedFile;
use App\Models\FileChunk;
use App\Services\FileExtractionService;
use App\Services\ChunkingService;
use App\Services\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Job xử lý file đã upload
 * 
 * Quy trình:
 * 1. Extract text từ file (PDF, Word, TXT, Image)
 * 2. Chia text thành chunks
 * 3. Tạo embeddings cho mỗi chunk
 * 4. Lưu chunks và embeddings vào database
 */
class ProcessUploadedFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Số lần thử lại nếu job fail
     */
    public int $tries = 3;

    /**
     * Thời gian timeout cho job (giây)
     */
    public int $timeout = 300;

    /**
     * Uploaded file cần xử lý
     */
    public UploadedFile $uploadedFile;

    /**
     * Create a new job instance.
     */
    public function __construct(UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
    }

    /**
     * Execute the job.
     */
    public function handle(
        FileExtractionService $extractor,
        ChunkingService $chunker,
        EmbeddingService $embedder
    ): void {
        $file = $this->uploadedFile;

        try {
            // Cập nhật status thành processing
            $file->update(['status' => 'processing']);

            Log::channel('rag')->info("JOB START: Processing file ID {$file->id} ({$file->filename})");
            Log::info("Bắt đầu xử lý file: {$file->filename} (ID: {$file->id})");

            // 1. Trích xuất text từ file
            // Dùng Storage facade để lấy path đúng theo disk config
            $filePath = Storage::path($file->file_path);

            if (!file_exists($filePath)) {
                throw new \Exception("File không tồn tại: {$filePath}");
            }

            $extractedText = $extractor->extract($filePath, $file->mime_type);

            if (empty($extractedText)) {
                Log::channel('rag')->error("Extraction returned empty text for file {$file->id}");
                throw new \Exception("Không trích xuất được text từ file");
            }

            $charCount = mb_strlen($extractedText);
            Log::channel('rag')->info("Extraction success: $charCount chars extracted. Preview: " . mb_substr($extractedText, 0, 100) . "...");
            Log::info("Đã trích xuất được " . $charCount . " ký tự từ file");

            // Lưu text đã trích xuất
            if (str_starts_with($file->mime_type, 'image/')) {
                $file->update(['image_description' => $extractedText]);
            } else {
                $file->update(['original_content' => $extractedText]);
            }

            // 2. Chia text thành chunks
            $chunks = $chunker->chunk($extractedText);

            $chunkCount = count($chunks);
            Log::channel('rag')->info("Chunking success: $chunkCount chunks created from file {$file->id}");
            Log::info("Đã chia thành " . $chunkCount . " chunks");

            if (empty($chunks)) {
                Log::channel('rag')->error("Chunking failed: 0 chunks created");
                throw new \Exception("Không thể chia text thành chunks");
            }

            // 3. Tạo embeddings cho tất cả chunks (batch)
            Log::channel('rag')->info("Embedding batch start: $chunkCount chunks");
            $embeddings = $embedder->embedBatch($chunks);
            Log::channel('rag')->info("Embedding batch finish");

            if (count($embeddings) !== count($chunks)) {
                Log::warning("Số lượng embeddings (" . count($embeddings) . ") khác với số chunks (" . count($chunks) . ")");
            }

            // 4. Lưu chunks và embeddings vào database
            DB::transaction(function () use ($file, $chunks, $embeddings) {
                // Xóa chunks cũ (nếu có - trường hợp retry)
                FileChunk::where('uploaded_file_id', $file->id)->delete();

                foreach ($chunks as $index => $chunkText) {
                    $embedding = $embeddings[$index] ?? null;

                    if (empty($embedding)) {
                        Log::warning("Chunk {$index} không có embedding, bỏ qua");
                        continue;
                    }

                    // Convert embedding array thành JSON string cho MariaDB
                    $embeddingJson = json_encode($embedding);

                    // Insert với raw SQL để sử dụng VEC_FromText
                    DB::insert("
                        INSERT INTO file_chunks (uploaded_file_id, chunk_index, chunk_text, embedding, created_at, updated_at)
                        VALUES (?, ?, ?, VEC_FromText(?), NOW(), NOW())
                    ", [$file->id, $index, $chunkText, $embeddingJson]);
                }
            });

            // Cập nhật status thành completed
            $file->update([
                'status' => 'completed',
                'error_message' => null
            ]);

            Log::channel('rag')->info("JOB COMPLETED: File {$file->id} processed successfully");
            Log::info("Hoàn thành xử lý file: {$file->filename} (ID: {$file->id})");

        } catch (\Exception $e) {
            Log::error("Lỗi xử lý file {$file->filename}: " . $e->getMessage());

            $file->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            // Re-throw để job có thể retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::channel('rag')->error("JOB FAILED: File {$this->uploadedFile->id}. Error: " . $exception->getMessage());
        Log::error("Job ProcessUploadedFileJob failed cho file ID {$this->uploadedFile->id}: " . $exception->getMessage());

        $this->uploadedFile->update([
            'status' => 'failed',
            'error_message' => 'Đã thử ' . $this->tries . ' lần nhưng không thành công: ' . $exception->getMessage()
        ]);
    }
}
