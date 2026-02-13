<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use App\Models\AgentSystem;
use App\Models\BrandAgent;
use App\Models\Brand;
use App\Models\SystemPrompt;
use App\Models\UploadedFile;
use App\Contracts\RagServiceInterface;
use App\Services\VectorSearchService;
use App\Services\CanopyRagService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GeminiChatController extends Controller
{
    private RagServiceInterface $ragService;
    private VectorSearchService $vectorSearchService;
    private CanopyRagService $canopyRagService;

    public function __construct(RagServiceInterface $ragService, VectorSearchService $vectorSearchService, CanopyRagService $canopyRagService)
    {
        $this->ragService = $ragService;
        $this->vectorSearchService = $vectorSearchService;
        $this->canopyRagService = $canopyRagService;
    }

    public function stream(Request $request)
    {
        $convId = $request->input('convId');
        $userInput = $request->input('message');
        $agentType = $request->input('agentType');
        $agentId = $request->input('agentId');
        $brandId = $request->input('brandId');
        $fileIds = $request->input('file_ids'); // Nhận danh sách file ID từ frontend
        $userId = $request->user() ? $request->user()->id : null;

        // --- Prompt Building Logic - Lấy từ bảng system_prompts ---
        $prompt = SystemPrompt::getPromptOrDefault('default_assistant', 'Bạn là trợ lý ảo.');

        if ($agentType === 'canopy') {
            if ($agentId) {
                $brandAgent = BrandAgent::find($agentId);
                if ($brandAgent) {
                    $prompt = $brandAgent->instruction ?? $prompt;

                    if ($brandAgent->is_include && $brandId) {
                        $brand = Brand::find($brandId);
                        if ($brand) {
                            // Lấy prompt intro từ bảng system_prompts
                            $prompt .= SystemPrompt::getPromptOrDefault('brand_data_intro', "\n\nHãy nhớ toàn bộ thông tin về thương hiệu bên dưới để tạo câu trả lời phù hợp:\n");

                            $rootData = $brand->root_data ?? [];
                            $trunkData = $brand->trunk_data ?? [];

                            if (is_array($rootData)) {
                                foreach ($rootData as $val) {
                                    if (is_string($val) && !empty($val))
                                        $prompt .= $val . "\n\n";
                                }
                            }
                            if (is_array($trunkData)) {
                                foreach ($trunkData as $val) {
                                    if (is_string($val) && !empty($val))
                                        $prompt .= $val . "\n\n";
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $agentConfig = null;
            if ($agentId) {
                $agentConfig = AgentSystem::find($agentId);
            }
            if (!$agentConfig && $agentType) {
                $agentConfig = AgentSystem::where('agent_type', $agentType)->first();
            }

            if ($agentConfig) {
                $prompt = $agentConfig->prompt ?? $prompt;
            }
        }

        $sequence = ['root1', 'root2', 'root3', 'trunk1', 'trunk2'];
        $currentIndex = array_search($agentType, $sequence);

        if ($brandId && $currentIndex !== false && $currentIndex > 0) {
            $brand = Brand::find($brandId);
            if ($brand) {
                $rootData = $brand->root_data ?? [];
                $trunkData = $brand->trunk_data ?? [];
                $contextParts = [];

                for ($i = 0; $i < $currentIndex; $i++) {
                    $prevType = $sequence[$i];
                    $val = null;

                    if (str_starts_with($prevType, 'root')) {
                        $val = $rootData[$prevType] ?? null;
                    } elseif (str_starts_with($prevType, 'trunk')) {
                        $val = $trunkData[$prevType] ?? null;
                    }

                    if (!empty($val)) {
                        $contextParts[] = "$prevType: $val";
                    }
                }

                if (!empty($contextParts)) {
                    // Lấy prompt intro từ bảng system_prompts
                    $contextIntro = SystemPrompt::getPromptOrDefault('context_steps_intro', "\n\nHãy ghi nhớ các thông tin thương hiệu đã xác nhận bên dưới để tạo câu trả lời tiếp theo phù hợp:\n");
                    $prompt .= $contextIntro . implode("\n", $contextParts);
                }
            }
        }

        // --- HYBRID RAG STRATEGY ---
        // System Agent + Gemini: RAG local ở trên, OpenAI Vector Store ở dưới
        // Brand Agent (canopy): Chỉ RAG local nếu có
        $localRagContent = '';
        $openaiRagContent = '';
        $vectorId = null;

        // Lấy vector_id từ agent
        if ($agentType === 'canopy') {
            // Brand Agent - lấy vector_id nếu có
            if (isset($brandAgent) && $brandAgent) {
                $vectorId = $brandAgent->vector_id ?? null;
            }
        } else {
            // System Agent
            if (isset($agentConfig) && $agentConfig) {
                $vectorId = $agentConfig->vector_id ?? null;
            }
        }

        // Nếu có vector_id (OpenAI Vector Store), gọi search
        if (!empty($vectorId)) {
            $openaiRagContent = $this->searchVectorStore($vectorId, $userInput);
        }
        // --- End HYBRID RAG ---

        // --- End Prompt Building Logic ---

        // 1. Create or Find Conversation
        if (!$convId || $convId === 'new') {
            $chat = $this->createConversation($agentType, $agentId, $userInput, $brandId, $userId);
            $convId = $chat->id;
        } else {
            $chat = Chat::where('id', $convId)->where('brand_id', $brandId)->first();
            if (!$chat) {
                $chat = $this->createConversation($agentType, $agentId, $userInput, $brandId, $userId);
                $convId = $chat->id;
            }
        }

        // === Đính kèm nội dung file vào tin nhắn ===
        // Logic mới: Chỉ lấy các file trong fileIds được gửi lên từ frontend
        $attachedFiles = collect([]);

        if (!empty($fileIds) && is_array($fileIds)) {
            // Đợi các file này xử lý xong
            $this->ragService->waitForFiles($fileIds);

            // Lấy nội dung
            $attachedFiles = \App\Models\UploadedFile::forChat($chat->id)
                ->whereIn('id', $fileIds)
                ->completed()
                ->get();
        }

        /* Logic cũ (đã bỏ)
        // Đợi các file đang processing hoàn tất
        $this->ragService->waitForPendingFiles('App\\Models\\Chat', $chat->id);

        // Lấy toàn bộ text đã trích xuất từ file đính kèm
        $attachedFiles = \App\Models\UploadedFile::forChat($chat->id)
            ->completed()
            ->get();
        */

        if ($attachedFiles->isNotEmpty()) {
            $fileTexts = [];
            foreach ($attachedFiles as $file) {
                if ($file->isImage()) {
                    // Hình: dùng mô tả từ Gemini Vision
                    $desc = $file->image_description;
                    if (!empty($desc)) {
                        $fileTexts[] = "Nội dung của hình đính kèm \"{$file->filename}\" là: {$desc}";
                    }
                } else {
                    // File khác: lấy text đã trích xuất
                    $content = $file->original_content;
                    if (!empty($content)) {
                        $fileTexts[] = "Nội dung tài liệu \"{$file->filename}\":\n{$content}";
                    }
                }
            }

            if (!empty($fileTexts)) {
                $userInput .= "\nĐây là các nội dung trong tài liệu đính kèm:\n" . implode("\n\n", $fileTexts);
            }
        }

        // === RAG Agent-Level cho Canopy ===
        // Nếu đang chat với canopy agent, kiểm tra agent có file đính kèm (uploaded_files)
        // Nếu có → reformulate question + search chunks → nối vào $userInput
        // (SONG SONG với chat-level file ở trên, không thay thế)
        if ($agentType === 'canopy' && $agentId) {
            $userInput = $this->canopyRagService->buildRagEnhancedInput(
                $userInput,
                (int) $agentId,
                $chat->id
            );
        }

        // === Inject OpenAI Vector Store cho System Agent ===
        if ($agentType !== 'canopy' && !empty($openaiRagContent)) {
            $ragIntro = SystemPrompt::getPromptOrDefault('rag_context_intro', "\n\nCác tài liệu tham khảo từ hệ thống (có thể sử dụng nếu phù hợp với câu hỏi):\n");
            $prompt .= $ragIntro . $openaiRagContent;
        }
        // === End RAG ===

        // Save User Message (bao gồm cả nội dung file đính kèm)
        Message::create([
            'chat_id' => $chat->id,
            'role' => 'user',
            'content' => $userInput
        ]);

        // 3. Prepare Gemini History
        $messages = Message::where('chat_id', $chat->id)->orderBy('created_at', 'asc')->get();
        $contents = [];
        foreach ($messages as $msg) {
            if (empty($msg->content))
                continue;
            $role = ($msg->role === 'user') ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $msg->content]
                ]
            ];
        }

        // 4. Stream Response from Gemini
        // === LOGGING ===
        $log = null;
        $loggingService = null;
        try {
            if (config('app.enable_chat_logging') || env('ENABLE_CHAT_LOGGING')) {
                $loggingService = new \App\Services\ChatLoggingService();

                // Resolve Brand Name
                $bName = null;
                if ($brandId) {
                    $b = \App\Models\Brand::find($brandId);
                    $bName = $b?->name;
                }

                // Resolve Agent Name
                $aName = null;
                if (isset($brandAgent) && $brandAgent) {
                    $aName = $brandAgent->name;
                } elseif (isset($agentConfig) && $agentConfig) {
                    $aName = $agentConfig->name;
                }

                // Get last user message content
                $lastUserContent = '';
                // User input was added to contents via loop over $messages?
                // Or $request->input('message')?
                // Check code: $messages = Message::where('chat_id', $chat->id)...
                // If new message was saved before this?
                // Let's assume passed $userInput or retrieve from create message logic
                // If controller saves message first, then it's in DB.
                // In ChatStreamController, $userInput was available.
                // In GeminiChatController, let's use $messages collection or find last user message.
                // Or just check $contents array end?
                // $contents is built from $messages.
                // If $contents has user role at end, take it.
                $lastPart = end($contents);
                if ($lastPart && $lastPart['role'] === 'user') {
                    $lastUserContent = $lastPart['parts'][0]['text'] ?? '';
                }

                $log = $loggingService->log(
                    $chat->id ?? null,
                    $userId ?? null,
                    $brandId,
                    $bName,
                    $agentId,
                    $aName,
                    $agentType,
                    config('services.gemini.chat_model'),
                    $lastUserContent,
                    $prompt ?? ''
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gemini Chat Logging Failed: " . $e->getMessage());
        }

        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.chat_model');

        return response()->stream(function () use ($chat, $prompt, $contents, $log, $loggingService, $apiKey, $model) {
            // Disable output buffering
            while (ob_get_level()) {
                ob_end_flush();
            }

            // Check API Key
            if (empty($apiKey)) {
                $errData = ['type' => 'response.output_text.delta', 'delta' => '[Lỗi: GEMINI_API_KEY chưa được cấu hình trong .env]'];
                echo "data: " . json_encode($errData) . "\n\n";
                flush();
                return;
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:streamGenerateContent?alt=sse&key=" . $apiKey;

            $payload = [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ],
                'contents' => $contents
            ];

            // Send initial metadata
            echo json_encode(['db_chat_id' => $chat->id, 'event' => 'metadata', 'conv_id' => $chat->id]) . "\n\n";
            flush();

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            $buffer = '';
            $hasError = false;

            // For logging accumulation
            $fullResponse = '';
            $inputTokens = 0;
            $outputTokens = 0;

            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) use (&$buffer, &$hasError, &$fullResponse, &$inputTokens, &$outputTokens) {
                $buffer .= $chunk;

                // Process complete lines
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = substr($buffer, 0, $pos);
                    $buffer = substr($buffer, $pos + 1);
                    $line = trim($line);

                    if (empty($line))
                        continue;

                    // SSE data line
                    if (strpos($line, 'data: ') === 0) {
                        $json = substr($line, 6);
                        if ($json === '[DONE]')
                            continue;

                        $data = json_decode($json, true);

                        // Check for error in response
                        if (isset($data['error'])) {
                            $hasError = true;
                            $errMsg = $data['error']['message'] ?? 'Unknown error';
                            $errData = ['type' => 'response.output_text.delta', 'delta' => "[Lỗi Gemini: $errMsg]"];
                            echo "data: " . json_encode($errData) . "\n\n";
                            flush();
                            continue;
                        }

                        // Check usage metadata
                        if (isset($data['usageMetadata'])) {
                            $inputTokens = $data['usageMetadata']['promptTokenCount'] ?? 0;
                            $outputTokens = $data['usageMetadata']['candidatesTokenCount'] ?? 0;
                        }

                        // Extract text content
                        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                            $text = $data['candidates'][0]['content']['parts'][0]['text'];

                            // Accumulate for log
                            $fullResponse .= $text;
                            $outData = [
                                'type' => 'response.output_text.delta',
                                'delta' => $text
                            ];
                            echo "data: " . json_encode($outData) . "\n\n";
                            flush();
                        }
                    }
                    // Check for raw JSON error (non-SSE error response)
                    elseif ($line[0] === '{') {
                        $data = json_decode($line, true);
                        if (isset($data['error'])) {
                            $hasError = true;
                            $errMsg = $data['error']['message'] ?? json_encode($data['error']);
                            $errData = ['type' => 'response.output_text.delta', 'delta' => "[Lỗi API: $errMsg]"];
                            echo "data: " . json_encode($errData) . "\n\n";
                            flush();
                        }
                    }
                }
                return strlen($chunk);
            });

            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                $errorMsg = curl_error($ch);
                Log::error('Gemini Curl Error: ' . $errorMsg);
                $errData = ['type' => 'response.output_text.delta', 'delta' => "[Lỗi kết nối: $errorMsg]"];
                echo "data: " . json_encode($errData) . "\n\n";
                flush();
            }

            curl_close($ch);

            // Update Log
            if ($log && $loggingService) {
                $loggingService->updateLog($log, $fullResponse, $inputTokens, $outputTokens);
            }

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no'
        ]);
    }

    private function createConversation($agentType, $agentId, $firstMessage, $brandId, $userId)
    {
        return Chat::create([
            'user_id' => $userId,
            'brand_id' => $brandId,
            'agent_id' => (int) $agentId,
            'agent_type' => $agentType,
            'model' => 'Gemini',
            'title' => 'Phiên làm việc ' . date('Y/m/d H:i:s'),
            'conversation_id' => 'gemini_' . uniqid() . '_' . Str::random(8)
        ]);
    }

    /**
     * Gọi OpenAI Vector Store Search API để lấy top 5 chunks liên quan
     */
    private function searchVectorStore($vectorId, $query)
    {
        $apiKey = env('OPENAI_API_KEY');

        if (empty($apiKey)) {
            Log::warning('OPENAI_API_KEY not set for vector search');
            return '';
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(10)
                ->post("https://api.openai.com/v1/vector_stores/{$vectorId}/search", [
                    'query' => $query,
                    'max_num_results' => 5
                ]);

            if (!$response->successful()) {
                Log::error('Vector Store Search Error: ' . $response->body());
                return '';
            }

            $data = $response->json();
            $chunks = $data['data'] ?? [];

            if (empty($chunks)) {
                return '';
            }

            // Format chunks với điểm phù hợp
            $formattedChunks = [];
            foreach ($chunks as $index => $chunk) {
                $score = $chunk['score'] ?? 0;
                $scorePercent = round($score * 100, 1);
                $filename = $chunk['filename'] ?? 'unknown';

                // Lấy nội dung text từ content array
                $textContent = '';
                if (isset($chunk['content']) && is_array($chunk['content'])) {
                    foreach ($chunk['content'] as $contentPart) {
                        if (isset($contentPart['text'])) {
                            $textContent .= $contentPart['text'] . ' ';
                        }
                    }
                }
                $textContent = trim($textContent);

                if (!empty($textContent)) {
                    $formattedChunks[] = "[Chunk " . ($index + 1) . " - Điểm phù hợp: {$scorePercent}% - File: {$filename}]\n{$textContent}";
                }
            }

            return implode("\n\n", $formattedChunks);

        } catch (\Exception $e) {
            Log::error('Vector Store Search Exception: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Format RAG chunks từ MariaDB thành text để inject vào prompt
     * Prompt: "Các thông tin có thể liên quan..."
     * 
     * @param array $chunks Mảng chunks từ VectorSearchService
     * @return string Text đã format
     */
    private function formatRagChunks(array $chunks): string
    {
        if (empty($chunks)) {
            return '';
        }

        $formattedChunks = [];
        foreach ($chunks as $index => $chunk) {
            $scorePercent = round(($chunk['score'] ?? 0) * 100, 1);
            $filename = $chunk['filename'] ?? 'unknown';
            $text = $chunk['chunk_text'] ?? '';

            $formattedChunks[] = "--- Tài liệu " . ($index + 1) . " (Độ liên quan: {$scorePercent}%, File: {$filename}) ---\n{$text}";
        }

        return "Các thông tin có thể liên quan (từ file đã upload, có thể tham khảo nếu phù hợp với câu hỏi):\n\n" . implode("\n\n", $formattedChunks);
    }
}
