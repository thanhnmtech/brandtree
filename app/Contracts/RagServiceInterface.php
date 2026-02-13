<?php

namespace App\Contracts;

/**
 * Interface định nghĩa các phương thức cho RAG Service
 * 
 * Cho phép dễ dàng chuyển đổi giữa:
 * - LocalRagService: Xử lý trực tiếp trên server Laravel với MariaDB Vector
 * - ApiRagService: Gọi đến RAG microservice riêng khi cần scale
 */
interface RagServiceInterface
{
    /**
     * Upload và xử lý file (async - dispatch job)
     * 
     * @param string $filePath Đường dẫn file đã upload
     * @param string $mimeType MIME type của file
     * @param string $attachableType Class name của model (Chat hoặc BrandAgent)
     * @param int $attachableId ID của Chat hoặc BrandAgent
     * @return array{file_id: int, status: string} Thông tin file đã tạo
     */
    public function processFile(string $filePath, string $mimeType, string $attachableType, int $attachableId): array;

    /**
     * Kiểm tra status của file
     * 
     * @param int $fileId ID của uploaded_file
     * @return array{status: string, progress?: int, error?: string} Trạng thái xử lý
     */
    public function getFileStatus(int $fileId): array;

    /**
     * Tìm chunks liên quan cho query
     * 
     * @param string $query Câu hỏi của user
     * @param string $attachableType Class name của model (Chat hoặc BrandAgent)
     * @param int $attachableId ID của Chat hoặc BrandAgent
     * @param int $limit Số chunks tối đa cần lấy
     * @return array<array{chunk_text: string, filename: string, score: float}> Danh sách chunks liên quan
     */
    public function search(string $query, string $attachableType, int $attachableId, int $limit = 5): array;

    /**
     * Đợi tất cả file pending/processing xử lý xong
     * 
     * Dùng trong luồng wait-before-response: khi user gửi message,
     * hệ thống đợi các file đang xử lý hoàn tất trước khi tạo response
     * 
     * @param string $attachableType Class name của model (Chat hoặc BrandAgent)
     * @param int $attachableId ID của Chat hoặc BrandAgent
     * @param int $maxWaitSeconds Thời gian tối đa chờ
     * @return bool True nếu tất cả file đã completed, false nếu timeout
     */
    public function waitForPendingFiles(string $attachableType, int $attachableId, int $maxWaitSeconds = 30): bool;

    /**
     * Đợi danh sách file cụ thể xử lý xong
     * 
     * @param array $fileIds Danh sách ID file cần đợi
     * @param int $maxWaitSeconds Thời gian tối đa chờ
     * @return bool True nếu tất cả file đã completed
     */
    public function waitForFiles(array $fileIds, int $maxWaitSeconds = 30): bool;

    /**
     * Xóa file và chunks liên quan
     * 
     * @param int $fileId ID của uploaded_file cần xóa
     * @return bool True nếu xóa thành công
     */
    public function deleteFile(int $fileId): bool;

    /**
     * Lấy danh sách file đã upload cho một context
     * 
     * @param string $attachableType Class name của model (Chat hoặc BrandAgent)
     * @param int $attachableId ID của Chat hoặc BrandAgent
     * @return array Danh sách files
     */
    public function getFiles(string $attachableType, int $attachableId): array;

    /**
     * Kiểm tra có thể upload thêm file không (dựa trên limit)
     * 
     * @param string $attachableType Class name của model (Chat hoặc BrandAgent)
     * @param int $attachableId ID của Chat hoặc BrandAgent
     * @return bool True nếu có thể upload thêm
     */
    public function canUploadMore(string $attachableType, int $attachableId): bool;
}
