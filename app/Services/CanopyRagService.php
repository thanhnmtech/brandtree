<?php

namespace App\Services;

use App\Models\UploadedFile;
use App\Models\BrandAgent;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service xử lý RAG agent-level cho canopy agent
 * 
 * Khi agent canopy có file đính kèm (uploaded_files với attachable_type=BrandAgent),
 * service này sẽ:
 * 1. Kiểm tra agent có file không
 * 2. Tạo câu hỏi hoàn chỉnh từ lịch sử hội thoại (Gemini Flash Lite)
 * 3. Tìm chunks liên quan qua VectorSearchService
 * 4. Nối chunks vào tin nhắn user
 */
class CanopyRagService
{
    private VectorSearchService $vectorSearchService;

    public function __construct(VectorSearchService $vectorSearchService)
    {
        $this->vectorSearchService = $vectorSearchService;
    }

    /**
     * Kiểm tra agent có file đính kèm (đã xử lý xong) không
     * 
     * Query: uploaded_files WHERE attachable_type='App\Models\BrandAgent' 
     *        AND attachable_id=$agentId AND status='completed'
     * 
     * @param int $agentId ID của BrandAgent
     * @return bool
     */
    public function agentHasFiles(int $agentId): bool
    {
        return UploadedFile::where('attachable_type', BrandAgent::class)
            ->where('attachable_id', $agentId)
            ->where('status', 'completed')
            ->whereHas('chunks')
            ->exists();
    }

    /**
     * Tạo câu hỏi hoàn chỉnh từ lịch sử hội thoại
     * 
     * Gọi Gemini 2.0 Flash Lite để viết lại câu hỏi cuối cùng
     * thành câu hỏi độc lập, đầy đủ ngữ cảnh.
     * 
     * @param array $recentMessages Mảng tin nhắn gần nhất [{role, content}]
     * @param string $latestQuestion Câu hỏi mới nhất của user
     * @return string Câu hỏi đã viết lại (hoặc câu gốc nếu lỗi)
     */
    public function reformulateQuestion(array $recentMessages, string $latestQuestion): string
    {
        // Nếu không có lịch sử, trả về câu gốc (không cần reformulate)
        if (empty($recentMessages)) {
            return $latestQuestion;
        }

        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            Log::warning('CanopyRAG: GEMINI_API_KEY chưa cấu hình, dùng câu hỏi gốc');
            return $latestQuestion;
        }

        // Tạo lịch sử hội thoại dạng text
        $historyText = '';
        foreach ($recentMessages as $msg) {
            $role = $msg['role'] === 'user' ? 'User' : 'Assistant';
            $content = mb_substr($msg['content'], 0, 500); // Giới hạn mỗi tin 500 ký tự
            $historyText .= "{$role}: {$content}\n";
        }

        // Prompt cho Gemini Flash Lite
        $prompt = "Dựa vào lịch sử hội thoại bên dưới, hãy viết lại câu hỏi cuối cùng của user thành một câu hỏi độc lập, đầy đủ ngữ cảnh, không cần đọc lịch sử cũng hiểu được. Chỉ trả về câu hỏi đã viết lại, không giải thích gì thêm.\n\nLịch sử hội thoại:\n{$historyText}\nCâu hỏi cuối cùng:\n{$latestQuestion}";

        try {
            // Gọi Gemini 2.0 Flash Lite (non-streaming)
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=' . $apiKey;

            $response = Http::withOptions(['verify' => false])
                ->timeout(10)
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 200,
                        'temperature' => 0.1, // Rất thấp để output chính xác
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $reformulated = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if (!empty($reformulated)) {
                    $reformulated = trim($reformulated);
                    Log::channel('rag')->info("CanopyRAG: Reformulated question", [
                        'original' => $latestQuestion,
                        'reformulated' => $reformulated
                    ]);
                    return $reformulated;
                }
            }

            Log::warning('CanopyRAG: Gemini Flash Lite không trả về kết quả, dùng câu gốc');
            return $latestQuestion;

        } catch (\Exception $e) {
            Log::error('CanopyRAG: Lỗi reformulate question: ' . $e->getMessage());
            return $latestQuestion; // Fallback về câu gốc
        }
    }

    /**
     * Orchestrate toàn bộ flow RAG agent-level:
     * 1. Kiểm tra agent có file
     * 2. Reformulate câu hỏi
     * 3. Tìm chunks liên quan
     * 4. Nối chunks vào userInput
     * 
     * @param string $userInput Tin nhắn gốc của user
     * @param int $agentId ID của BrandAgent
     * @param int $chatId ID cuộc chat hiện tại (để lấy lịch sử)
     * @param int $maxChunks Số chunks tối đa (mặc định 10)
     * @return string UserInput đã được bổ sung chunks (hoặc giữ nguyên nếu không có file)
     */
    public function buildRagEnhancedInput(string $userInput, int $agentId, int $chatId, int $maxChunks = 10): string
    {
        // Bước 1: Kiểm tra agent có file không
        if (!$this->agentHasFiles($agentId)) {
            return $userInput;
        }

        Log::channel('rag')->info("CanopyRAG: Agent {$agentId} có file, bắt đầu RAG search");

        // Bước 2: Lấy 5 tin nhắn gần nhất để reformulate
        $recentMessages = Message::where('chat_id', $chatId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->reverse() // Đảo lại thứ tự thời gian tăng dần
            ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
            ->values()
            ->toArray();

        // Bước 3: Reformulate câu hỏi
        $standaloneQuestion = $this->reformulateQuestion($recentMessages, $userInput);

        // Bước 4: Tìm chunks liên quan bằng câu hỏi hoàn chỉnh
        $chunks = $this->vectorSearchService->searchForAgent($agentId, $standaloneQuestion, $maxChunks);

        if (empty($chunks)) {
            Log::channel('rag')->info("CanopyRAG: Không tìm thấy chunks liên quan cho agent {$agentId}");
            return $userInput;
        }

        // Bước 5: Format chunks và nối vào userInput
        $chunksText = $this->formatChunks($chunks);

        Log::channel('rag')->info("CanopyRAG: Tìm thấy " . count($chunks) . " chunks cho agent {$agentId}", [
            'standalone_question' => $standaloneQuestion,
            'chunk_count' => count($chunks)
        ]);

        // Nối: câu gốc + intro + chunks (thứ tự giảm dần theo điểm liên quan)
        return $userInput . "\n\nCó thể tham khảo các nội dung đính kèm có thể liên quan bên dưới:\n\n" . $chunksText;
    }

    /**
     * Format mảng chunks thành text, thứ tự giảm dần theo điểm liên quan
     * 
     * @param array $chunks Mảng chunks từ VectorSearchService
     * @return string Text đã format
     */
    private function formatChunks(array $chunks): string
    {
        $formatted = [];
        foreach ($chunks as $index => $chunk) {
            $scorePercent = round(($chunk['score'] ?? 0) * 100, 1);
            $filename = $chunk['filename'] ?? 'unknown';
            $text = $chunk['chunk_text'] ?? '';

            $formatted[] = "--- Chunk " . ($index + 1) . " (Độ liên quan: {$scorePercent}%, File: {$filename}) ---\n{$text}";
        }

        return implode("\n\n", $formatted);
    }
}
