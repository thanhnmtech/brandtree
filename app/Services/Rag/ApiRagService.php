<?php

namespace App\Services\Rag;

use App\Contracts\RagServiceInterface;
use Illuminate\Support\Facades\Http;

/**
 * API implementation - gọi đến RAG microservice riêng
 * 
 * Dùng khi tách service ra server riêng để scale.
 * Cấu hình trong .env:
 *   RAG_DRIVER=api
 *   RAG_API_URL=https://rag.yourdomain.com
 *   RAG_API_KEY=your-secret-key
 * 
 * TODO: Implement đầy đủ khi cần tách RAG service
 */
class ApiRagService implements RagServiceInterface
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('rag.api.base_url');
        $this->apiKey = config('rag.api.api_key');
        $this->timeout = config('rag.api.timeout', 30);
    }

    /**
     * Upload và xử lý file qua API
     */
    public function processFile(string $filePath, string $mimeType, string $attachableType, int $attachableId): array
    {
        $response = Http::withToken($this->apiKey)
            ->timeout($this->timeout)
            ->attach('file', file_get_contents($filePath), basename($filePath))
            ->post("{$this->baseUrl}/files/process", [
                'mime_type' => $mimeType,
                'attachable_type' => $attachableType,
                'attachable_id' => $attachableId,
            ]);

        return $response->json() ?? ['file_id' => 0, 'status' => 'error'];
    }

    /**
     * Kiểm tra status của file
     */
    public function getFileStatus(int $fileId): array
    {
        $response = Http::withToken($this->apiKey)
            ->timeout($this->timeout)
            ->get("{$this->baseUrl}/files/{$fileId}/status");

        return $response->json() ?? ['status' => 'error'];
    }

    /**
     * Tìm chunks liên quan cho query
     */
    public function search(string $query, string $attachableType, int $attachableId, int $limit = 5): array
    {
        $response = Http::withToken($this->apiKey)
            ->timeout($this->timeout)
            ->post("{$this->baseUrl}/search", [
                'query' => $query,
                'attachable_type' => $attachableType,
                'attachable_id' => $attachableId,
                'limit' => $limit,
            ]);

        return $response->json() ?? [];
    }

    /**
     * Đợi tất cả file pending xử lý xong
     */
    public function waitForPendingFiles(string $attachableType, int $attachableId, int $maxWaitSeconds = 30): bool
    {
        $response = Http::withToken($this->apiKey)
            ->timeout($maxWaitSeconds + 5)
            ->post("{$this->baseUrl}/files/wait", [
                'attachable_type' => $attachableType,
                'attachable_id' => $attachableId,
                'max_wait_seconds' => $maxWaitSeconds,
            ]);

        return $response->json('completed') ?? false;
    }

    /**
     * Xóa file và chunks liên quan
     */
    public function deleteFile(int $fileId): bool
    {
        $response = Http::withToken($this->apiKey)
            ->timeout($this->timeout)
            ->delete("{$this->baseUrl}/files/{$fileId}");

        return $response->successful();
    }

    /**
     * Lấy danh sách file đã upload
     */
    public function getFiles(string $attachableType, int $attachableId): array
    {
        $response = Http::withToken($this->apiKey)
            ->timeout($this->timeout)
            ->get("{$this->baseUrl}/files", [
                'attachable_type' => $attachableType,
                'attachable_id' => $attachableId,
            ]);

        return $response->json() ?? [];
    }

    /**
     * Kiểm tra có thể upload thêm file không
     */
    public function canUploadMore(string $attachableType, int $attachableId): bool
    {
        $response = Http::withToken($this->apiKey)
            ->timeout($this->timeout)
            ->get("{$this->baseUrl}/files/can-upload", [
                'attachable_type' => $attachableType,
                'attachable_id' => $attachableId,
            ]);

        return $response->json('can_upload') ?? false;
    }
}
