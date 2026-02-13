<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service tạo embeddings sử dụng Gemini Embedding API
 * 
 * Model: text-embedding-004
 * Dimensions: 768
 */
class EmbeddingService
{
    private string $model;
    private int $dimensions;

    public function __construct()
    {
        $this->model = config('rag.embedding.model', 'text-embedding-004');
        $this->dimensions = config('rag.embedding.dimensions', 768);
    }

    /**
     * Tạo embedding cho một đoạn text
     * 
     * @param string $text Text cần embed
     * @return array Vector embedding (768 dimensions)
     * @throws \Exception Nếu có lỗi khi gọi API
     */
    public function embed(string $text): array
    {
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            throw new \Exception('GEMINI_API_KEY chưa được cấu hình trong .env');
        }

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:embedContent?key={$apiKey}";

            Log::channel('rag')->info("Embedding single content. Model: {$this->model}. Text length: " . mb_strlen($text));
            $startTime = microtime(true);

            $response = Http::timeout(30)->post($url, [
                'model' => "models/{$this->model}",
                'content' => [
                    'parts' => [
                        ['text' => $text]
                    ]
                ],
                'taskType' => 'RETRIEVAL_DOCUMENT',
                'outputDimensionality' => $this->dimensions ?? 768
            ]);
            $duration = round(microtime(true) - $startTime, 2);
            Log::channel('rag')->info("Embedding finished in {$duration}s");

            if (!$response->successful()) {
                Log::error('Gemini Embedding API Error: ' . $response->body());
                throw new \Exception('Lỗi gọi Gemini Embedding API: ' . $response->status());
            }

            $data = $response->json();
            $embedding = $data['embedding']['values'] ?? null;

            if (empty($embedding)) {
                throw new \Exception('Không nhận được embedding từ Gemini API');
            }

            return $embedding;

        } catch (\Exception $e) {
            Log::error("Lỗi tạo embedding: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Tạo embedding cho nhiều đoạn text (batch)
     * Tối ưu API calls bằng cách gọi batch
     * 
     * @param array<string> $texts Mảng các text cần embed
     * @return array<array> Mảng các vectors
     */
    public function embedBatch(array $texts): array
    {
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            throw new \Exception('GEMINI_API_KEY chưa được cấu hình trong .env');
        }

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:batchEmbedContents?key={$apiKey}";

            Log::channel('rag')->info("Embedding batch. Count: " . count($texts));
            $startTime = microtime(true);

            // Chuẩn bị requests cho batch
            $requests = [];
            foreach ($texts as $text) {
                $requests[] = [
                    'model' => "models/{$this->model}",
                    'content' => [
                        'parts' => [
                            ['text' => $text]
                        ]
                    ],
                    'taskType' => 'RETRIEVAL_DOCUMENT',
                    'outputDimensionality' => $this->dimensions ?? 768
                ];
            }

            $response = Http::timeout(60)->post($url, [
                'requests' => $requests
            ]);
            $duration = round(microtime(true) - $startTime, 2);
            Log::channel('rag')->info("Batch embedding finished in {$duration}s");

            if (!$response->successful()) {
                Log::error('Gemini Batch Embedding API Error: ' . $response->body());
                throw new \Exception('Lỗi gọi Gemini Batch Embedding API: ' . $response->status());
            }

            $data = $response->json();
            $embeddings = [];

            if (isset($data['embeddings'])) {
                foreach ($data['embeddings'] as $embedding) {
                    $embeddings[] = $embedding['values'] ?? [];
                }
            }

            // Kiểm tra số lượng embeddings trả về
            if (count($embeddings) !== count($texts)) {
                Log::warning("Số lượng embeddings trả về ({count($embeddings)}) khác với số text gửi đi ({count($texts)})");
            }

            return $embeddings;

        } catch (\Exception $e) {
            Log::error("Lỗi tạo batch embedding: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Tạo embedding cho query (tìm kiếm)
     * Dùng taskType = RETRIEVAL_QUERY thay vì RETRIEVAL_DOCUMENT
     * 
     * @param string $query Query text
     * @return array Vector embedding
     */
    public function embedQuery(string $query): array
    {
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            throw new \Exception('GEMINI_API_KEY chưa được cấu hình trong .env');
        }

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:embedContent?key={$apiKey}";

            $response = Http::timeout(30)->post($url, [
                'model' => "models/{$this->model}",
                'content' => [
                    'parts' => [
                        ['text' => $query]
                    ]
                ],
                'taskType' => 'RETRIEVAL_QUERY',
                'outputDimensionality' => $this->dimensions ?? 768
            ]);

            if (!$response->successful()) {
                Log::error('Gemini Query Embedding API Error: ' . $response->body());
                throw new \Exception('Lỗi gọi Gemini Embedding API: ' . $response->status());
            }

            $data = $response->json();
            $embedding = $data['embedding']['values'] ?? null;

            if (empty($embedding)) {
                throw new \Exception('Không nhận được embedding từ Gemini API');
            }

            return $embedding;

        } catch (\Exception $e) {
            Log::error("Lỗi tạo query embedding: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy số dimensions của model embedding
     */
    public function getDimensions(): int
    {
        return $this->dimensions;
    }
}
