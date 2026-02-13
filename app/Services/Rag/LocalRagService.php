<?php

namespace App\Services\Rag;

use App\Contracts\RagServiceInterface;
use App\Models\UploadedFile;
use App\Models\FileChunk;
use App\Services\VectorSearchService;
use App\Jobs\ProcessUploadedFileJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Local implementation - xử lý trực tiếp trên server Laravel
 * 
 * Sử dụng MariaDB Vector, Gemini Embedding, PHP text extraction.
 * Đây là driver mặc định khi RAG_DRIVER=local
 */
class LocalRagService implements RagServiceInterface
{
    private VectorSearchService $vectorSearchService;

    public function __construct(VectorSearchService $vectorSearchService)
    {
        $this->vectorSearchService = $vectorSearchService;
    }

    /**
     * Upload và xử lý file (async - dispatch job)
     * 
     * @param string $filePath Đường dẫn file đã upload (relative to storage/app)
     * @param string $mimeType MIME type của file
     * @param string $attachableType Class name của model (Chat hoặc BrandAgent)
     * @param int $attachableId ID của Chat hoặc BrandAgent
     * @return array{file_id: int, status: string}
     */
    public function processFile(string $filePath, string $mimeType, string $attachableType, int $attachableId): array
    {
        // Lấy thông tin user hiện tại
        $userId = auth()->id();
        $brandId = null;

        // Xác định brand_id từ attachable
        if ($attachableType === 'App\\Models\\Chat') {
            $chat = \App\Models\Chat::find($attachableId);
            $brandId = $chat?->brand_id;
        } elseif ($attachableType === 'App\\Models\\BrandAgent') {
            $agent = \App\Models\BrandAgent::find($attachableId);
            $brandId = $agent?->brand_id;
        }

        // Lấy file size
        $fileSize = Storage::exists($filePath) ? Storage::size($filePath) : 0;

        // Tạo record UploadedFile
        $uploadedFile = UploadedFile::create([
            'user_id' => $userId,
            'brand_id' => $brandId,
            'attachable_type' => $attachableType,
            'attachable_id' => $attachableId,
            'filename' => basename($filePath),
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'status' => 'pending',
        ]);

        Log::channel('rag')->info("File created: {$uploadedFile->id}, Path: $filePath, Size: $fileSize, User: $userId");

        // Dispatch job để xử lý async
        ProcessUploadedFileJob::dispatch($uploadedFile);
        Log::channel('rag')->info("Job dispatched for file {$uploadedFile->id}");

        return [
            'file_id' => $uploadedFile->id,
            'status' => 'pending',
        ];
    }

    /**
     * Kiểm tra status của file
     * 
     * @param int $fileId ID của uploaded_file
     * @return array{status: string, progress?: int, error?: string}
     */
    public function getFileStatus(int $fileId): array
    {
        $file = UploadedFile::find($fileId);

        if (!$file) {
            return ['status' => 'not_found'];
        }

        return [
            'status' => $file->status,
            'error' => $file->error_message,
        ];
    }

    /**
     * Tìm chunks liên quan cho query
     * 
     * @param string $query Câu hỏi của user
     * @param string $attachableType Class name của model
     * @param int $attachableId ID của Chat hoặc BrandAgent
     * @param int $limit Số chunks tối đa
     * @return array<array{chunk_text: string, filename: string, score: float}>
     */
    public function search(string $query, string $attachableType, int $attachableId, int $limit = 5): array
    {
        return $this->vectorSearchService->search($query, $attachableType, $attachableId, $limit);
    }

    /**
     * Đợi tất cả file pending/processing xử lý xong
     * 
     * @param string $attachableType Class name của model
     * @param int $attachableId ID của Chat hoặc BrandAgent
     * @param int $maxWaitSeconds Thời gian tối đa chờ
     * @return bool True nếu tất cả file đã completed
     */
    public function waitForPendingFiles(string $attachableType, int $attachableId, int $maxWaitSeconds = 30): bool
    {
        $startTime = time();
        $pollInterval = config('rag.wait.poll_interval_us', 500000);

        while (time() - $startTime < $maxWaitSeconds) {
            $pendingCount = UploadedFile::where('attachable_type', $attachableType)
                ->where('attachable_id', $attachableId)
                ->whereIn('status', ['pending', 'processing'])
                ->count();

            if ($pendingCount === 0) {
                return true; // Tất cả file đã xong
            }

            usleep($pollInterval); // Đợi rồi check lại
        }

        // Timeout - tiếp tục với những file đã completed
        Log::warning("File processing timeout for {$attachableType}:{$attachableId}");
        return false;
    }

    /**
     * Đợi danh sách file cụ thể xử lý xong
     * 
     * @param array $fileIds Danh sách ID file cần đợi
     * @param int $maxWaitSeconds Thời gian tối đa chờ
     * @return bool True nếu tất cả file đã completed
     */
    public function waitForFiles(array $fileIds, int $maxWaitSeconds = 30): bool
    {
        if (empty($fileIds)) {
            return true;
        }

        $startTime = time();
        $pollInterval = config('rag.wait.poll_interval_us', 500000);

        while (time() - $startTime < $maxWaitSeconds) {
            $pendingCount = UploadedFile::whereIn('id', $fileIds)
                ->whereIn('status', ['pending', 'processing'])
                ->count();

            if ($pendingCount === 0) {
                return true; // Tất cả file đã xong
            }

            usleep($pollInterval); // Đợi rồi check lại
        }

        // Timeout
        Log::warning("File processing timeout for files: " . implode(',', $fileIds));
        return false;
    }

    /**
     * Xóa file và chunks liên quan
     * 
     * @param int $fileId ID của uploaded_file cần xóa
     * @return bool True nếu xóa thành công
     */
    public function deleteFile(int $fileId): bool
    {
        $file = UploadedFile::find($fileId);

        if (!$file) {
            return false;
        }

        // Xóa file vật lý
        if ($file->file_path && Storage::exists($file->file_path)) {
            Storage::delete($file->file_path);
        }

        // Xóa record (cascade sẽ xóa chunks)
        $file->delete();

        return true;
    }

    /**
     * Lấy danh sách file đã upload cho một context
     * 
     * @param string $attachableType Class name của model
     * @param int $attachableId ID của Chat hoặc BrandAgent
     * @return array Danh sách files
     */
    public function getFiles(string $attachableType, int $attachableId): array
    {
        return UploadedFile::where('attachable_type', $attachableType)
            ->where('attachable_id', $attachableId)
            ->get()
            ->toArray();
    }

    /**
     * Kiểm tra có thể upload thêm file không
     * 
     * @param string $attachableType Class name của model
     * @param int $attachableId ID của Chat hoặc BrandAgent
     * @return bool True nếu có thể upload thêm
     */
    public function canUploadMore(string $attachableType, int $attachableId): bool
    {
        $maxFiles = config('rag.upload.max_files_per_context', 20);

        $currentCount = UploadedFile::where('attachable_type', $attachableType)
            ->where('attachable_id', $attachableId)
            ->count();

        return $currentCount < $maxFiles;
    }

    /**
     * Format kết quả search thành text cho prompt
     * 
     * @param array $chunks Kết quả từ search()
     * @return string Text đã format
     */
    public function formatChunksForPrompt(array $chunks): string
    {
        return $this->vectorSearchService->formatChunksForPrompt($chunks);
    }
}
