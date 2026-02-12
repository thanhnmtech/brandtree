<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\UploadedFile;
use App\Models\FileChunk;

/**
 * Service tìm kiếm vector trong MariaDB
 * 
 * Sử dụng MariaDB Vector capabilities:
 * - VEC_DISTANCE_COSINE để tính khoảng cách giữa các vectors
 * - VEC_FromText để convert JSON array thành vector
 */
class VectorSearchService
{
    private EmbeddingService $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
    }

    /**
     * Tìm các chunks liên quan nhất với query
     * 
     * @param string $query Câu hỏi/query từ user
     * @param string $attachableType Class name của model (App\Models\Chat hoặc App\Models\BrandAgent)
     * @param int $attachableId ID của Chat hoặc BrandAgent
     * @param int $limit Số chunks tối đa cần lấy
     * @return array<array{chunk_text: string, filename: string, score: float}> Danh sách chunks liên quan
     */
    public function search(string $query, string $attachableType, int $attachableId, int $limit = 5): array
    {
        try {
            // 1. Tạo embedding cho query
            $queryVector = $this->embeddingService->embedQuery($query);
            $queryVectorJson = json_encode($queryVector);

            // 2. Tìm kiếm trong MariaDB với vector distance
            // Lưu ý: MariaDB Vector sử dụng VEC_DISTANCE_COSINE trả về distance (0 = giống nhau hoàn toàn)
            // Nên score = 1 - distance để chuyển thành similarity
            $results = DB::select("
                SELECT 
                    fc.id,
                    fc.chunk_text,
                    fc.chunk_index,
                    uf.filename,
                    uf.id as file_id,
                    VEC_DISTANCE_COSINE(fc.embedding, VEC_FromText(?)) as distance
                FROM file_chunks fc
                JOIN uploaded_files uf ON uf.id = fc.uploaded_file_id
                WHERE uf.attachable_type = ?
                  AND uf.attachable_id = ?
                  AND uf.status = 'completed'
                ORDER BY distance ASC
                LIMIT ?
            ", [$queryVectorJson, $attachableType, $attachableId, $limit]);

            // 3. Format kết quả
            $chunks = [];
            foreach ($results as $result) {
                // Chuyển distance thành similarity score (1 - distance)
                $score = 1 - ($result->distance ?? 0);

                $chunks[] = [
                    'chunk_text' => $result->chunk_text,
                    'filename' => $result->filename,
                    'score' => round($score, 4),
                    'chunk_index' => $result->chunk_index,
                    'file_id' => $result->file_id,
                ];
            }

            return $chunks;

        } catch (\Exception $e) {
            Log::error("Lỗi vector search: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm chunks cho Chat
     * Shorthand method cho search() với Chat attachable type
     * 
     * @param int $chatId ID của Chat
     * @param string $query Câu hỏi từ user
     * @param int $limit Số chunks tối đa
     * @return array Danh sách chunks liên quan
     */
    public function searchForChat(int $chatId, string $query, int $limit = 5): array
    {
        return $this->search($query, 'App\\Models\\Chat', $chatId, $limit);
    }

    /**
     * Tìm kiếm chunks cho BrandAgent
     * Shorthand method cho search() với BrandAgent attachable type
     * 
     * @param int $agentId ID của BrandAgent
     * @param string $query Câu hỏi từ user
     * @param int $limit Số chunks tối đa
     * @return array Danh sách chunks liên quan
     */
    public function searchForAgent(int $agentId, string $query, int $limit = 5): array
    {
        return $this->search($query, 'App\\Models\\BrandAgent', $agentId, $limit);
    }

    /**
     * Format kết quả search thành text để inject vào prompt
     * 
     * @param array $chunks Kết quả từ search()
     * @return string Text đã format
     */
    public function formatChunksForPrompt(array $chunks): string
    {
        if (empty($chunks)) {
            return '';
        }

        $formattedChunks = [];
        foreach ($chunks as $index => $chunk) {
            $scorePercent = round($chunk['score'] * 100, 1);
            $filename = $chunk['filename'];
            $text = $chunk['chunk_text'];

            $formattedChunks[] = "[Tài liệu " . ($index + 1) . " - Độ phù hợp: {$scorePercent}% - File: {$filename}]\n{$text}";
        }

        return implode("\n\n", $formattedChunks);
    }

    /**
     * Kiểm tra xem có chunks nào trong context không
     * 
     * @param string $attachableType Class name của model
     * @param int $attachableId ID của Chat hoặc BrandAgent
     * @return bool True nếu có chunks
     */
    public function hasChunks(string $attachableType, int $attachableId): bool
    {
        return UploadedFile::where('attachable_type', $attachableType)
            ->where('attachable_id', $attachableId)
            ->where('status', 'completed')
            ->whereHas('chunks')
            ->exists();
    }
}
