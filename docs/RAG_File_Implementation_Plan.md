# Triển khai tính năng RAG File cho Chat AI và Agent Canopy

## Tóm tắt

Tính năng cho phép người dùng upload file (PDF, Word, TXT, hình ảnh) vào **đoạn chat** hoặc khi **tạo/sửa Agent canopy**. Nội dung file sẽ được:
1. Trích xuất text (bằng PHP libraries)
2. Mô tả hình ảnh (bằng Gemini Vision)
3. Chunking và embedding (bằng Gemini Embedding API)
4. Lưu vector vào MariaDB
5. Query tìm chunks liên quan khi tạo câu trả lời

### Giới hạn
- **Tối đa 10MB/file**
- **Tối đa 20 file/chat** hoặc **20 file/agent**
- **Scope**: Chỉ search trong file của chat/agent hiện tại

---

## Quyết định đã xác nhận

> [!NOTE]
> **Vector Store Strategy: Hybrid**
> - **SystemAgent** (root1, root2, trunk1, trunk2...): Tiếp tục dùng **OpenAI Vector Store** (giữ nguyên code hiện tại)
> - **BrandAgent** (canopy): Dùng **MariaDB Vector** với RAG file tự xây dựng

> [!NOTE]
> **Xử lý bất đồng bộ với Wait-Before-Response:**
> 1. User upload file → File được xử lý **song song** (async) trong background
> 2. User tiếp tục gõ chat và nhấn Enter
> 3. Khi gửi message → Hệ thống **đợi** các file đang processing hoàn tất
> 4. Sau khi tất cả file completed → Mới tạo và trả lời response
>
> ➡️ User không bị block khi upload, nhưng đảm bảo RAG có đủ context trước khi trả lời.

---

## Proposed Changes

### Database Schema

#### [NEW] [create_uploaded_files_table.php](file:///d:/laragon/www/brandtree/database/migrations/2026_02_09_000001_create_uploaded_files_table.php)

```php
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
    $table->text('original_content')->nullable(); // Text extracted
    $table->text('image_description')->nullable(); // Mô tả từ Gemini Vision
    
    $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
    $table->text('error_message')->nullable();
    
    $table->timestamps();
    
    $table->index(['attachable_type', 'attachable_id']);
});
```

#### [NEW] [create_file_chunks_table.php](file:///d:/laragon/www/brandtree/database/migrations/2026_02_09_000002_create_file_chunks_table.php)

```php
Schema::create('file_chunks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('uploaded_file_id')->constrained('uploaded_files')->onDelete('cascade');
    $table->unsignedInteger('chunk_index');
    $table->text('chunk_text');
    $table->vector('embedding', 768); // Gemini embedding dimension
    $table->timestamps();
    
    $table->index(['uploaded_file_id', 'chunk_index']);
});

// Thêm HNSW index cho vector search (raw SQL)
DB::statement('ALTER TABLE file_chunks ADD VECTOR INDEX embedding_idx (embedding) M=16 DISTANCE=COSINE');
```

---

### Models

#### [NEW] [UploadedFile.php](file:///d:/laragon/www/brandtree/app/Models/UploadedFile.php)

Eloquent model với:
- Polymorphic relationship `attachable()` → works với cả `Chat` và `BrandAgent`
- Relationship `chunks()`
- Scope `completed()`, `forChat($chatId)`, `forAgent($agentId)`

#### [NEW] [FileChunk.php](file:///d:/laragon/www/brandtree/app/Models/FileChunk.php)

Eloquent model với relationship `uploadedFile()`

#### [MODIFY] [Chat.php](file:///d:/laragon/www/brandtree/app/Models/Chat.php)

Thêm relationship:
```php
public function uploadedFiles()
{
    return $this->morphMany(UploadedFile::class, 'attachable');
}
```

#### [MODIFY] [BrandAgent.php](file:///d:/laragon/www/brandtree/app/Models/BrandAgent.php)

Thêm relationship:
```php
public function uploadedFiles()
{
    return $this->morphMany(UploadedFile::class, 'attachable');
}
```

---

### Scalable Architecture (Chuẩn bị cho tương lai)

> [!TIP]
> **Kiến trúc Interface-based** cho phép dễ dàng chuyển RAG service ra server riêng khi cần scale.
> Hiện tại dùng **Local Driver**, sau này có thể switch sang **API Driver** chỉ bằng config.

#### [NEW] [config/rag.php](file:///d:/laragon/www/brandtree/config/rag.php)

```php
<?php
return [
    // Driver: 'local' hoặc 'api'
    'driver' => env('RAG_DRIVER', 'local'),
    
    // Config cho API driver (khi tách service ra server riêng)
    'api' => [
        'base_url' => env('RAG_API_URL', 'https://rag.example.com'),
        'api_key' => env('RAG_API_KEY'),
        'timeout' => env('RAG_API_TIMEOUT', 30),
    ],
    
    // Config cho embedding
    'embedding' => [
        'model' => env('EMBEDDING_MODEL', 'text-embedding-004'),
        'dimensions' => 768,
    ],
    
    // Config cho chunking
    'chunking' => [
        'max_tokens' => 500,
        'overlap' => 50,
    ],
];
```

#### Contracts (Interfaces)

#### [NEW] [Contracts/RagServiceInterface.php](file:///d:/laragon/www/brandtree/app/Contracts/RagServiceInterface.php)

```php
<?php
namespace App\Contracts;

interface RagServiceInterface
{
    /**
     * Upload và xử lý file (async)
     * @return array{file_id: int, status: string}
     */
    public function processFile(string $filePath, string $mimeType, string $attachableType, int $attachableId): array;
    
    /**
     * Kiểm tra status của file
     * @return array{status: string, progress?: int}
     */
    public function getFileStatus(int $fileId): array;
    
    /**
     * Tìm chunks liên quan cho query
     * @return array<array{chunk_text: string, filename: string, score: float}>
     */
    public function search(string $query, string $attachableType, int $attachableId, int $limit = 5): array;
    
    /**
     * Đợi tất cả file pending xử lý xong
     */
    public function waitForPendingFiles(string $attachableType, int $attachableId, int $maxWaitSeconds = 30): bool;
    
    /**
     * Xóa file và chunks liên quan
     */
    public function deleteFile(int $fileId): bool;
}
```

#### Implementations

#### [NEW] [Services/Rag/LocalRagService.php](file:///d:/laragon/www/brandtree/app/Services/Rag/LocalRagService.php)

```php
<?php
namespace App\Services\Rag;

use App\Contracts\RagServiceInterface;

/**
 * Local implementation - xử lý trực tiếp trên server Laravel
 * Sử dụng MariaDB Vector, Gemini Embedding, PHP text extraction
 */
class LocalRagService implements RagServiceInterface
{
    public function __construct(
        private FileExtractionService $extractor,
        private ChunkingService $chunker,
        private EmbeddingService $embedder,
        private VectorSearchService $searcher
    ) {}
    
    public function processFile(...): array { /* dispatch job */ }
    public function getFileStatus(...): array { /* query DB */ }
    public function search(...): array { /* MariaDB vector search */ }
    public function waitForPendingFiles(...): bool { /* poll DB */ }
    public function deleteFile(...): bool { /* delete from DB */ }
}
```

#### [NEW] [Services/Rag/ApiRagService.php](file:///d:/laragon/www/brandtree/app/Services/Rag/ApiRagService.php)

```php
<?php
namespace App\Services\Rag;

use App\Contracts\RagServiceInterface;
use Illuminate\Support\Facades\Http;

/**
 * API implementation - gọi đến RAG microservice riêng
 * Dùng khi tách service ra server riêng để scale
 */
class ApiRagService implements RagServiceInterface
{
    private string $baseUrl;
    private string $apiKey;
    
    public function __construct()
    {
        $this->baseUrl = config('rag.api.base_url');
        $this->apiKey = config('rag.api.api_key');
    }
    
    public function processFile(string $filePath, ...): array
    {
        // Upload file qua API
        return Http::withToken($this->apiKey)
            ->attach('file', file_get_contents($filePath), basename($filePath))
            ->post("{$this->baseUrl}/files/process", [...])
            ->json();
    }
    
    public function search(string $query, ...): array
    {
        return Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/search", ['query' => $query, ...])
            ->json();
    }
    
    // ... các methods khác tương tự
}
```

#### Service Provider Registration

#### [MODIFY] [Providers/AppServiceProvider.php](file:///d:/laragon/www/brandtree/app/Providers/AppServiceProvider.php)

```php
use App\Contracts\RagServiceInterface;
use App\Services\Rag\LocalRagService;
use App\Services\Rag\ApiRagService;

public function register()
{
    // Đăng ký RAG service dựa trên config driver
    $this->app->singleton(RagServiceInterface::class, function ($app) {
        return match(config('rag.driver')) {
            'api' => $app->make(ApiRagService::class),
            default => $app->make(LocalRagService::class),
        };
    });
}
```

#### Cách sử dụng trong Controllers

```php
// GeminiChatController.php - Inject interface, không phụ thuộc implementation
class GeminiChatController extends Controller
{
    public function __construct(
        private RagServiceInterface $ragService  // ← Interface, không phải class cụ thể
    ) {}
    
    public function stream(Request $request)
    {
        // ...
        if ($agentType === 'canopy') {
            // Đợi file xử lý xong
            $this->ragService->waitForPendingFiles('App\\Models\\Chat', $chat->id);
            
            // Search chunks
            $chunks = $this->ragService->search($userInput, 'App\\Models\\Chat', $chat->id);
            
            // Format và inject vào prompt
            // ...
        }
    }
}
```

#### Migration Path (Khi cần scale)

```
Bước 1: Deploy RAG microservice lên server riêng
        └── Python FastAPI + pgvector / Qdrant / Pinecone

Bước 2: Cập nhật .env trên Laravel server:
        RAG_DRIVER=api
        RAG_API_URL=https://rag.yourdomain.com
        RAG_API_KEY=your-secret-key

Bước 3: Done! Không cần sửa code trong Controllers
```

---

### Services (Core Logic - Local Driver)

#### [NEW] [FileExtractionService.php](file:///d:/laragon/www/brandtree/app/Services/FileExtractionService.php)

Trích xuất text từ file:
```php
class FileExtractionService
{
    public function extract(string $filePath, string $mimeType): string
    {
        return match(true) {
            str_contains($mimeType, 'pdf') => $this->extractPdf($filePath),
            str_contains($mimeType, 'word') || str_contains($mimeType, 'document') 
                => $this->extractWord($filePath),
            $mimeType === 'text/plain' => file_get_contents($filePath),
            str_starts_with($mimeType, 'image/') => $this->describeImage($filePath),
            default => throw new UnsupportedFileTypeException($mimeType)
        };
    }
    
    private function extractPdf(string $path): string 
    {
        // Sử dụng smalot/pdfparser
    }
    
    private function extractWord(string $path): string
    {
        // Sử dụng phpoffice/phpword
    }
    
    private function describeImage(string $path): string
    {
        // Gọi Gemini 2.0 Flash với vision capability
    }
}
```

#### [NEW] [ChunkingService.php](file:///d:/laragon/www/brandtree/app/Services/ChunkingService.php)

Chia text thành chunks:
```php
class ChunkingService
{
    public function chunk(string $text, int $maxTokens = 500, int $overlap = 50): array
    {
        // Recursive character text splitter
        // Returns array of chunk strings
    }
}
```

#### [NEW] [EmbeddingService.php](file:///d:/laragon/www/brandtree/app/Services/EmbeddingService.php)

Tạo embedding với Gemini:
```php
class EmbeddingService
{
    public function embed(string $text): array
    {
        // Gọi Gemini Embedding API (text-embedding-004)
        // Return 768-dimensional vector
    }
    
    public function embedBatch(array $texts): array
    {
        // Batch embedding để tối ưu API calls
    }
}
```

#### [NEW] [VectorSearchService.php](file:///d:/laragon/www/brandtree/app/Services/VectorSearchService.php)

Query tìm chunks liên quan:
```php
class VectorSearchService
{
    public function search(string $query, string $attachableType, int $attachableId, int $limit = 5): array
    {
        $queryVector = $this->embeddingService->embed($query);
        
        // MariaDB vector search
        return DB::select("
            SELECT fc.chunk_text, uf.filename,
                   VEC_DISTANCE_COSINE(fc.embedding, VEC_FromText(?)) as distance
            FROM file_chunks fc
            JOIN uploaded_files uf ON uf.id = fc.uploaded_file_id
            WHERE uf.attachable_type = ? AND uf.attachable_id = ?
              AND uf.status = 'completed'
            ORDER BY distance ASC
            LIMIT ?
        ", [json_encode($queryVector), $attachableType, $attachableId, $limit]);
    }
}
```

---

### Jobs (Background Processing)

#### [NEW] [ProcessUploadedFileJob.php](file:///d:/laragon/www/brandtree/app/Jobs/ProcessUploadedFileJob.php)

Laravel Queue job xử lý file:
```php
class ProcessUploadedFileJob implements ShouldQueue
{
    public function handle(
        FileExtractionService $extractor,
        ChunkingService $chunker,
        EmbeddingService $embedder
    ) {
        // 1. Extract text
        // 2. Chunk text
        // 3. Embed chunks
        // 4. Save to file_chunks table
        // 5. Update status to 'completed'
    }
}
```

---

### Controllers

#### [NEW] [FileUploadController.php](file:///d:/laragon/www/brandtree/app/Http/Controllers/FileUploadController.php)

Endpoints:
- `POST /api/files/upload` - Upload file cho chat
- `POST /api/agents/{agent}/files` - Upload file cho agent
- `GET /api/files/{file}/status` - Check processing status
- `DELETE /api/files/{file}` - Xóa file

#### [MODIFY] [GeminiChatController.php](file:///d:/laragon/www/brandtree/app/Http/Controllers/GeminiChatController.php)

**Logic Hybrid RAG:**
```php
// Trong method stream()

// === HYBRID RAG STRATEGY ===
if ($agentType === 'canopy') {
    // BrandAgent: Dùng MariaDB Vector (RAG file tự xây dựng)
    
    // 1. Đợi tất cả file đang processing hoàn tất (wait-before-response)
    $this->waitForPendingFiles($chat->id, 'App\\Models\\Chat');
    
    // 2. Search trong MariaDB file_chunks
    $ragContent = $this->vectorSearchService->searchForChat($chat->id, $userInput);
    
    // 3. Nếu agent có is_include = true, thêm cả brand data
    // (giữ nguyên logic hiện tại)
    
} else {
    // SystemAgent (root1, root2, trunk1, trunk2...): Dùng OpenAI Vector Store
    // Giữ nguyên logic hiện tại với $this->searchVectorStore($vectorId, $userInput)
    if (!empty($vectorId)) {
        $ragContent = $this->searchVectorStore($vectorId, $userInput);
    }
}

// === WAIT FOR PENDING FILES ===
private function waitForPendingFiles(int $attachableId, string $attachableType, int $maxWaitSeconds = 30): void
{
    $startTime = time();
    
    while (time() - $startTime < $maxWaitSeconds) {
        $pendingCount = UploadedFile::where('attachable_type', $attachableType)
            ->where('attachable_id', $attachableId)
            ->whereIn('status', ['pending', 'processing'])
            ->count();
        
        if ($pendingCount === 0) {
            return; // Tất cả file đã xong
        }
        
        usleep(500000); // Đợi 0.5s rồi check lại
    }
    
    // Timeout - tiếp tục với những file đã completed
    Log::warning("File processing timeout for {$attachableType}:{$attachableId}");
}
```

#### [MODIFY] [ChatStreamController.php](file:///d:/laragon/www/brandtree/app/Http/Controllers/ChatStreamController.php)

Tương tự như `GeminiChatController` - áp dụng hybrid RAG strategy

---

### Frontend

#### [MODIFY] [chat.blade.php](file:///d:/laragon/www/brandtree/resources/views/livewire/chat.blade.php)

- Implement upload logic cho nút "+" (hiện có UI, chưa có logic)
- Hiển thị danh sách file đã upload
- Hiển thị trạng thái processing (pending/processing/completed)
- Cho phép xóa file

#### [MODIFY] [create-agent-modal.blade.php](file:///d:/laragon/www/brandtree/resources/views/brands/trees/partials/create-agent-modal.blade.php)

- Implement upload logic cho vùng "Tài liệu tham khảo" (hiện có UI, chưa có logic)
- Hiển thị file đã chọn
- Upload khi submit form

#### [MODIFY] [edit-agent-modal.blade.php](file:///d:/laragon/www/brandtree/resources/views/brands/trees/partials/edit-agent-modal.blade.php)

- Hiển thị file đã upload
- Cho phép thêm/xóa file

---

### Routes

#### [MODIFY] [web.php](file:///d:/laragon/www/brandtree/routes/web.php)

```php
// File Upload Routes
Route::middleware('auth')->group(function () {
    Route::post('/api/files/upload', [FileUploadController::class, 'uploadForChat']);
    Route::post('/brands/{brand}/agents/{agent}/files', [FileUploadController::class, 'uploadForAgent']);
    Route::get('/api/files/{file}/status', [FileUploadController::class, 'status']);
    Route::delete('/api/files/{file}', [FileUploadController::class, 'destroy']);
});
```

---

### Dependencies (Composer)

#### [MODIFY] [composer.json](file:///d:/laragon/www/brandtree/composer.json)

```json
{
    "require": {
        "smalot/pdfparser": "^2.0",
        "phpoffice/phpword": "^1.1"
    }
}
```

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────────┐
│                              USER ACTIONS                                │
├─────────────────────────────────────────────────────────────────────────┤
│  [Chat Page - Canopy Agent]           [Canopy - Create/Edit Agent]      │
│  Click "+" → Upload file              Drag/Drop tài liệu tham khảo      │
└──────────────┬────────────────────────────────────┬─────────────────────┘
               │                                    │
               ▼                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                        FileUploadController                              │
│  - Validate file (size ≤10MB, type, count ≤20)                          │
│  - Save to storage/app/uploads/                                          │
│  - Create UploadedFile record (status: pending)                          │
│  - Dispatch ProcessUploadedFileJob → Queue (ASYNC)                       │
│  - Return immediately → User tiếp tục chat                               │
└──────────────────────────────────┬──────────────────────────────────────┘
                                   │
                    ┌──────────────┴──────────────┐
                    ▼                             ▼
┌───────────────────────────────┐    ┌────────────────────────────────────┐
│  User tiếp tục gõ chat        │    │  ProcessUploadedFileJob (Queue)    │
│  và nhấn Enter                │    │  - Extract text (PHP)              │
│                               │    │  - Describe image (Gemini Vision)  │
│  ┌─────────────────────────┐  │    │  - Chunk (500 tokens)              │
│  │ Gửi message đến server  │  │    │  - Embed (Gemini Embedding)        │
│  └───────────┬─────────────┘  │    │  - Save to MariaDB Vector          │
│              │                │    │  - Update status = 'completed'     │
└──────────────┼────────────────┘    └────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│              GeminiChatController / ChatStreamController                 │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │  HYBRID RAG STRATEGY                                            │    │
│  ├─────────────────────────────────────────────────────────────────┤    │
│  │                                                                  │    │
│  │  if (agentType === 'canopy') {                                  │    │
│  │      // BrandAgent → MariaDB Vector                             │    │
│  │                                                                  │    │
│  │      1. waitForPendingFiles() ← ĐỢI FILE PROCESSING XONG        │    │
│  │         └── Poll DB mỗi 0.5s, timeout 30s                       │    │
│  │                                                                  │    │
│  │      2. VectorSearchService.searchForChat()                     │    │
│  │         └── VEC_DISTANCE_COSINE → Top 5 chunks                  │    │
│  │                                                                  │    │
│  │  } else {                                                        │    │
│  │      // SystemAgent (root1, trunk1...) → OpenAI Vector Store    │    │
│  │      └── searchVectorStore() (giữ nguyên code hiện tại)         │    │
│  │  }                                                               │    │
│  └─────────────────────────────────────────────────────────────────┘    │
│                                                                          │
│  3. Inject chunks vào prompt                                            │
│  4. Gọi Gemini/GPT với prompt đã enhanced                               │
│  5. Stream response về client                                            │
└─────────────────────────────────────────────────────────────────────────┘
```

### Luồng Wait-Before-Response chi tiết

```
Timeline khi user upload file và chat:

T=0s    User click "+" và chọn file
        └── Server nhận file, tạo UploadedFile (status: pending)
        └── Dispatch job to Queue
        └── Response ngay: { fileId: 123, status: 'pending' }

T=0.1s  User vẫn đang gõ chat...
        └── Background: Job bắt đầu xử lý file

T=2s    User nhấn Enter gửi message
        └── Server: waitForPendingFiles() bắt đầu

T=2.5s  Background: Job hoàn tất, update status = 'completed'

T=3s    Server: waitForPendingFiles() detect completed
        └── Tiếp tục search vector, tạo response

T=3.2s  Stream response bắt đầu về client
```

---

## Verification Plan

### Automated Tests

Do dự án hiện tại chưa có test suite, tôi đề xuất các bước manual testing chi tiết bên dưới.

### Manual Verification

#### Test 1: Upload file PDF trong Chat
1. Mở trang chat: `http://brandtree.test/brands/{slug}/chat/canopy/{agentId}/new`
2. Click nút "+" trước ô nhập tin nhắn
3. Chọn 1 file PDF (< 10MB)
4. Kiểm tra:
   - File hiển thị trong UI với trạng thái "Đang xử lý..."
   - Sau vài giây refresh, trạng thái đổi thành "Hoàn tất"
   - Database: Kiểm tra `uploaded_files` có record với status = 'completed'
   - Database: Kiểm tra `file_chunks` có các chunks với embedding

#### Test 2: RAG hoạt động trong Chat
1. Upload 1 file PDF chứa nội dung về "sản phẩm chuột gaming"
2. Đợi processing hoàn tất
3. Gửi tin nhắn: "Cho tôi biết về sản phẩm chuột gaming"
4. Kiểm tra: Câu trả lời của AI có tham khảo thông tin từ file đã upload

#### Test 3: Upload file hình ảnh
1. Upload 1 file hình ảnh sản phẩm
2. Đợi processing hoàn tất
3. Kiểm tra:
   - Database `uploaded_files.image_description` có mô tả từ Gemini Vision
   - `file_chunks` có chunk với embedding của mô tả đó

#### Test 4: Giới hạn file
1. Thử upload file > 10MB → Expect: Lỗi "File quá lớn"
2. Upload 20 file, thử upload file thứ 21 → Expect: Lỗi "Đã đạt giới hạn 20 file"

#### Test 5: Upload file khi tạo Agent Canopy
1. Vào trang `/brands/{slug}/canopy`
2. Click "Tạo Agent mới"
3. Điền tên, instruction
4. Upload 2 file tài liệu
5. Click "Tạo Agent"
6. Kiểm tra:
   - Agent tạo thành công
   - Database `uploaded_files` có 2 records với `attachable_type = BrandAgent`
7. Sử dụng agent đó, gửi câu hỏi liên quan đến tài liệu → AI tham khảo được nội dung

#### Test 6: Queue Worker
1. Kiểm tra queue worker đang chạy: `php artisan queue:work`
2. Upload file, kiểm tra job được xử lý trong queue
3. Nếu dùng database driver: kiểm tra bảng `jobs`

---

## Implementation Order

1. **Phase 1 - Database & Models** (Est: 1 giờ)
   - Migrations (`uploaded_files`, `file_chunks`)
   - Models với relationships

2. **Phase 2 - Config & Interface** (Est: 0.5 giờ) ← **MỚI**
   - `config/rag.php`
   - `Contracts/RagServiceInterface.php`
   - Service Provider registration

3. **Phase 3 - Services (Local Driver)** (Est: 3 giờ)
   - `FileExtractionService`
   - `ChunkingService`  
   - `EmbeddingService`
   - `VectorSearchService`
   - `LocalRagService` (implements interface)

4. **Phase 4 - Controller & Routes** (Est: 1 giờ)
   - `FileUploadController`
   - Routes

5. **Phase 5 - Queue Job** (Est: 1 giờ)
   - `ProcessUploadedFileJob`
   - Config queue

6. **Phase 6 - Integrate RAG** (Est: 1 giờ)
   - Modify `GeminiChatController` (inject `RagServiceInterface`)
   - Modify `ChatStreamController`

7. **Phase 7 - Frontend Chat** (Est: 2 giờ)
   - Implement upload logic
   - Display files & status
   - Delete file

8. **Phase 8 - Frontend Agent** (Est: 2 giờ)
   - Create modal: upload logic
   - Edit modal: display & manage files

**Tổng cộng: ~11.5 giờ**

---

## Future: API Driver (Khi cần scale)

Khi tính năng RAG quá nặng và cần tách ra server riêng:

1. **Tạo RAG Microservice** (Python recommended)
   - FastAPI + pgvector hoặc Qdrant
   - Endpoints: `/files/process`, `/files/status`, `/search`, `/files/{id}`

2. **Implement `ApiRagService.php`** trong Laravel
   - Gọi các endpoints trên qua HTTP

3. **Switch driver** trong `.env`:
   ```
   RAG_DRIVER=api
   RAG_API_URL=https://rag.yourdomain.com
   RAG_API_KEY=your-secret-key
   ```

4. **Done!** Controllers không cần sửa code.
