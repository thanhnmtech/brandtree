<?php

/**
 * Cấu hình RAG (Retrieval-Augmented Generation)
 * 
 * - Driver 'local': Xử lý trực tiếp trên server Laravel với MariaDB Vector
 * - Driver 'api': Gọi đến RAG microservice riêng (khi cần scale)
 */
return [
    // Driver: 'local' hoặc 'api'
    // - 'local': Dùng MariaDB Vector, Gemini Embedding, PHP text extraction
    // - 'api': Gọi đến RAG microservice riêng (khi cần scale)
    'driver' => env('RAG_DRIVER', 'local'),

    // Config cho API driver (khi tách service ra server riêng)
    'api' => [
        'base_url' => env('RAG_API_URL', 'https://rag.example.com'),
        'api_key' => env('RAG_API_KEY'),
        'timeout' => env('RAG_API_TIMEOUT', 30),
    ],

    // Config cho embedding (Gemini Embedding API)
    'embedding' => [
        // Model embedding của Gemini
        'model' => env('EMBEDDING_MODEL', 'gemini-embedding-001'),
        // Số chiều của vector embedding
        'dimensions' => 768,
    ],

    // Config cho chunking (chia text thành các đoạn nhỏ)
    'chunking' => [
        // Số tokens tối đa mỗi chunk
        'max_tokens' => 800,
        // Số tokens overlap giữa các chunk
        'overlap' => 200,
    ],

    // Giới hạn upload file
    'upload' => [
        // Kích thước tối đa mỗi file (bytes) - 10MB
        'max_file_size' => 10 * 1024 * 1024,
        // Số file tối đa cho mỗi chat/agent
        'max_files_per_context' => 20,
        // Các MIME types được phép
        'allowed_mime_types' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ],
    ],

    // Config cho wait-before-response
    'wait' => [
        // Thời gian tối đa chờ file processing xong (giây)
        'max_wait_seconds' => 30,
        // Khoảng thời gian giữa các lần poll (microseconds) - 0.5 giây
        'poll_interval_us' => 500000,
    ],
];
