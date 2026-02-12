# Tính năng Upload File & RAG cho Agent Canopy

> **Branch này bao gồm:** Upload file khi tạo/sửa agent canopy, upload file trong lúc chat, và tìm kiếm nội dung tài liệu liên quan (RAG) khi chat với agent canopy.

---

## Mục lục

1. [Tổng quan tính năng](#1-tổng-quan-tính-năng)
2. [Cấu trúc file thay đổi](#2-cấu-trúc-file-thay-đổi)
3. [Database migrations](#3-database-migrations)
4. [Cấu hình cần thiết trên Production](#4-cấu-hình-cần-thiết-trên-production)
5. [Hướng dẫn deploy](#5-hướng-dẫn-deploy)
6. [Kiểm tra sau deploy](#6-kiểm-tra-sau-deploy)

---

## 1. Tổng quan tính năng

### 1.1. Upload file cho Agent Canopy (Agent-level file)

- Khi **tạo** hoặc **sửa** agent canopy, admin có thể upload tài liệu tham khảo (PDF, DOC, DOCX, TXT).
- File sẽ được **trích xuất text** → **chia thành chunks** → **tạo embedding vector** — toàn bộ chạy ngầm qua Laravel Queue.
- Khi user chat với agent này, hệ thống **tự động tìm 10 chunks liên quan** nhất với câu hỏi và đính kèm vào tin nhắn.
- **Phạm vi ảnh hưởng:** Toàn bộ cuộc chat với agent đó (không chỉ 1 cuộc).

### 1.2. Upload file trong lúc chat (Chat-level file)

- User có thể upload file hoặc paste hình ảnh trực tiếp vào khung chat.
- Nội dung file được trích xuất và **đính kèm full text** vào tin nhắn user.
- **Phạm vi ảnh hưởng:** Chỉ cuộc chat hiện tại.

### 1.3. Hai loại file hoạt động SONG SONG

| Loại | Điều kiện | Lưu trữ | Cách xử lý khi chat |
|---|---|---|---|
| **Chat-level** | Upload trong khung chat | `attachable_type = App\Models\Chat` | Full text cộng vào tin nhắn |
| **Agent-level** | Upload khi tạo/sửa agent | `attachable_type = App\Models\BrandAgent` | RAG search top 10 chunks |

---

## 2. Cấu trúc file thay đổi

### Backend

| File | Thay đổi |
|---|---|
| `routes/web.php` | Thêm route `POST /brands/{brand}/agents/{agent}/files` |
| `app/Http/Controllers/BrandAgentController.php` | `store()` trả thêm `agent_id` |
| `app/Http/Controllers/FileUploadController.php` | `uploadForAgent()` chỉ chấp nhận PDF/Word/TXT |
| `app/Http/Controllers/ChatStreamController.php` | Thêm inject `CanopyRagService` cho RAG agent-level |
| `app/Http/Controllers/GeminiChatController.php` | Thêm inject `CanopyRagService` cho RAG agent-level |
| `app/Services/CanopyRagService.php` | **[MỚI]** Service RAG: reformulate question + search chunks |
| `app/Services/VectorSearchService.php` | Vector search MariaDB (VEC_DISTANCE_COSINE) |
| `app/Services/EmbeddingService.php` | Tạo embedding qua Gemini API |
| `app/Services/FileExtractionService.php` | Extract text từ PDF/Word/TXT/Image |
| `app/Services/Rag/LocalRagService.php` | Orchestrate RAG pipeline |
| `app/Jobs/ProcessUploadedFileJob.php` | Background job: extract → chunk → embed |
| `app/Models/UploadedFile.php` | Model file upload (polymorphic) |
| `app/Models/FileChunk.php` | Model chunk text + embedding |
| `config/rag.php` | Config RAG (chunking, embedding, upload limits) |

### Frontend

| File | Thay đổi |
|---|---|
| `resources/views/brands/trees/partials/create-agent-modal.blade.php` | Thêm UI chọn file khi tạo agent |
| `resources/views/brands/trees/partials/edit-agent-modal.blade.php` | Thêm UI quản lý file khi sửa agent |

### Database Migrations

| File | Bảng |
|---|---|
| `database/migrations/2026_02_09_000001_create_uploaded_files_table.php` | `uploaded_files` |
| `database/migrations/2026_02_09_000002_create_file_chunks_table.php` | `file_chunks` |

---

## 3. Database migrations

### Bảng `uploaded_files`

```
id, attachable_type, attachable_id, filename, original_name, 
file_path, file_size, mime_type, status (pending/processing/completed/failed),
original_content (LONGTEXT), image_description (LONGTEXT), 
error_message, timestamps
```

### Bảng `file_chunks`

```
id, uploaded_file_id (FK), chunk_text (TEXT), chunk_index (INT),
embedding (LONGBLOB), timestamps
```

> **Lưu ý:** Cột `embedding` lưu dưới dạng LONGBLOB. MariaDB 11.8+ hỗ trợ kiểu `VECTOR` native, nhưng hiện tại dùng LONGBLOB + `VEC_FromText()` / `VEC_DISTANCE_COSINE()` để tương thích.

---

## 4. Cấu hình cần thiết trên Production

### 4.1. Biến môi trường (.env)

Thêm hoặc kiểm tra các biến sau trong file `.env`:

```env
# === API Keys (BẮT BUỘC) ===
GEMINI_API_KEY=your_gemini_api_key       # Dùng cho: embedding, reformulate question, mô tả hình ảnh

# === Queue (BẮT BUỘC) ===
QUEUE_CONNECTION=database                 # PHẢI là 'database' hoặc 'redis' (KHÔNG được là 'sync')

# === RAG Config (Tùy chọn - đã có mặc định) ===
RAG_DRIVER=local                          # Mặc định: local
EMBEDDING_MODEL=gemini-embedding-001      # Mặc định: gemini-embedding-001
```

### 4.2. MariaDB version

- **Yêu cầu:** MariaDB **11.4+** (hỗ trợ VEC_DISTANCE_COSINE)
- Production đang dùng MariaDB **11.8.6** → ✅ OK

### 4.3. PHP Extensions

Đảm bảo các extension sau đã bật:

```
php-xml         # Cần cho phpoffice/phpword (đọc DOCX)
php-zip         # Cần cho phpoffice/phpword (đọc DOCX)
php-gd          # Cần cho xử lý image (tùy chọn)
php-curl        # Gọi API Gemini/OpenAI
php-mbstring    # Xử lý text đa ngôn ngữ
```

### 4.4. Composer Packages

Packages mới (đã khai báo trong `composer.json`):

```
smalot/pdfparser    ^2.12    # Extract text từ PDF
phpoffice/phpword   ^1.3     # Extract text từ DOC/DOCX
```

### 4.5. Storage

- File upload lưu tại: `storage/app/uploads/brand_{id}/user_{id}/`
- Đảm bảo thư mục `storage/app/uploads/` có quyền ghi (775 hoặc tương đương)

### 4.6. Queue Worker (QUAN TRỌNG)

File upload chạy xử lý ngầm qua queue. **BẮT BUỘC** phải có queue worker chạy liên tục:

```bash
# Chạy queue worker (dùng Supervisor để giữ tự động restart)
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

**Supervisor config mẫu** (`/etc/supervisor/conf.d/brandtree-worker.conf`):

```ini
[program:brandtree-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/brandtree/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/brandtree/storage/logs/worker.log
stopwaitsecs=3600
```

Sau khi tạo file config:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start brandtree-worker:*
```

---

## 5. Hướng dẫn deploy

Thứ tự thực hiện trên server production:

```bash
# 1. Pull code mới
git pull origin <branch-name>

# 2. Cài package PHP mới
composer install --no-dev --optimize-autoloader

# 3. Chạy migrations tạo bảng mới
php artisan migrate --force

# 4. Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Khởi động lại queue worker
sudo supervisorctl restart brandtree-worker:*

# 6. Kiểm tra quyền thư mục
chmod -R 775 storage/app/uploads
chown -R www-data:www-data storage/app/uploads
```

---

## 6. Kiểm tra sau deploy

### Checklist

- [ ] **Migrations:** Kiểm tra bảng `uploaded_files` và `file_chunks` đã tồn tại
- [ ] **Queue worker:** `php artisan queue:work --once` chạy không lỗi
- [ ] **Gemini API:** Kiểm tra `GEMINI_API_KEY` bằng route `/api/gemini/test`
- [ ] **Tạo agent + upload file:** Vào Canopy → Tạo Agent → Upload 1 file PDF → Kiểm tra file
- [ ] **Xem log RAG:** Truy cập `/debug/rag-logs` để xem log xử lý file
- [ ] **Chat test:** Chat với agent canopy đã có file → AI có tham khảo nội dung file không

### Kiểm tra nhanh qua CLI

```bash
# Kiểm tra bảng DB
php artisan tinker --execute="echo App\Models\UploadedFile::count();"

# Kiểm tra queue đang chạy
php artisan queue:monitor database

# Xem log RAG
tail -f storage/logs/rag_debug.log
```

---

## Lưu ý bảo mật

- Route `/debug/rag-logs` chỉ dùng debug, **nên xóa hoặc thêm middleware auth** trên production.
- File upload được validate MIME type ở cả frontend và backend.
- Agent-level file chỉ chấp nhận PDF/DOC/DOCX/TXT (không cho image).
- Chat-level file chấp nhận cả image (JPEG, PNG, GIF, WebP).
