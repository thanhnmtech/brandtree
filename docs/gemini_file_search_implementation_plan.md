# Kế hoạch triển khai Gemini File Search API

## Mục tiêu
Thay thế hoàn toàn cơ chế RAG tự host (MariaDB Vector) bằng **Google Gemini File Search API**. File sẽ được upload trực tiếp lên Google, và việc tìm kiếm/trích xuất thông tin sẽ do Gemini xử lý tự động khi chat.

## Quyết định kỹ thuật
- **Bỏ**: Bảng `file_chunks`, `VectorSearchService`, `EmbeddingService`, `ChunkingService`.
- **Thêm mới**: `GeminiRagService` để quản lý FileSearchStore và Upload file.
- **Lưu trữ**:
    - `Agent` (Canopy) sẽ có 1 `FileSearchStore` riêng.
    - `Chat` (Phiên chat) sẽ có 1 `FileSearchStore` riêng (nếu user upload file vào chat).
- **Luồng Chat**: Khi gọi Gemini, truyền danh sách `FileSearchStore` (của Agent + của Chat) vào tham số `tools`.

## Thay đổi Database

### 1. Bảng `uploaded_files`
- Thêm cột `gemini_uri` (string, nullable) - URI file trên Google.
- Thêm cột `gemini_name` (string, nullable) - Tên resource (vd: `files/abc-123`).

### 2. Bảng `brand_agents`
- Thêm cột `gemini_store_name` (string, nullable) - Tên Store (vd: `fileSearchStores/xyz-789`).

### 3. Bảng `chats`
- Thêm cột `gemini_store_name` (string, nullable) - Tên Store cho phiên chat.

### 4. Xóa bảng `file_chunks`
- Drop table `file_chunks` vì không còn dùng.

## Kiến trúc mới

### Services
#### `App\Services\Rag\GeminiRagService`
Implement `RagServiceInterface` (cần sửa lại interface cho phù hợp).
- `getOrCreateStore(string $displayName): string` (Trả về store name)
- `uploadFile(string $filePath, string $mimeType, string $storeName): array` (Upload và trả về file info)
- `deleteFile(string $geminiName): bool`
- `deleteStore(string $storeName): bool`

### Controllers
#### `FileUploadController`
- **Upload**:
    1. Xác định target (Agent hoặc Chat).
    2. Gọi `GeminiRagService` để lấy/tạo Store tương ứng.
    3. Gọi `GeminiRagService` để upload file vào Store đó.
    4. Lưu `gemini_name` vào DB `uploaded_files`.
- **Delete**:
    1. Gọi `GeminiRagService` để xóa file trên Google.
    2. Xóa record trong DB.

#### `GeminiChatController`
- **Stream**:
    1. Lấy `gemini_store_name` của Agent (nếu là Canopy Agent).
    2. Lấy `gemini_store_name` của Chat (nếu có).
    3. Construct payload `tools` cho Gemini API:
       ```json
       "tools": [
           {
               "google_search_retrieval": { "dynamic_retrieval_config": { "mode": "dynamic_retrieval_config_mode_unspecified" } } // Nếu cần
               // HOẶC
               "file_search": {
                   "file_search_store_names": ["sites/xxx", "sites/yyy"]
               }
           }
       ]
       ```
    4. Bỏ code tìm kiếm vector cũ.

## Kế hoạch thực hiện

### Phase 1: Database Migration
- Tạo migration thêm cột cho `uploaded_files`, `brand_agents`, `chats`.
- Tạo migration xóa `file_chunks`.

### Phase 2: Service Implementation
- Cập nhật `RagServiceInterface`.
- Viết `GeminiRagService`.
- Đăng ký service trong `AppServiceProvider`.

### Phase 3: Controller Updates
- Cập nhật `FileUploadController` để dùng service mới.
- Cập nhật `GeminiChatController` để inject `file_search` tool.

### Phase 4: Validations
- Test upload file (Agent & Chat).
- Test chat với file đã upload.
- Test xóa file.

## Verification Plan

### Automated Tests
*Hiện tại chưa có hệ thống test tự động, sẽ thực hiện manual test.*

### Manual Verification
1. **Test Database Migration**: Chạy migration, kiểm tra các cột mới trong database.
2. **Test Upload File vào Agent**:
    - Vào Brand -> Canopy -> Edit Agent.
    - Upload 1 file PDF.
    - Kiểm tra DB: `brand_agents.gemini_store_name` có dữ liệu, `uploaded_files.gemini_name` có dữ liệu.
    - Kiểm tra trên Google AI Studio (nếu có thể) hoặc log xem API trả về success không.
3. **Test Chat với Agent**:
    - Chat với Agent vừa upload file.
    - Hỏi nội dung trong file.
    - Kiểm tra câu trả lời có thông tin từ file không.
4. **Test Upload File vào Chat**:
    - Tạo chat mới.
    - Upload file vào chat.
    - Kiểm tra DB `chats.gemini_store_name`.
    - Chat và hỏi nội dung file.
5. **Test Xóa File**:
    - Xóa file khỏi Agent/Chat.
    - Kiểm tra DB record mất.
    - (Optional) Kiểm tra trên Google xem file mất chưa.
