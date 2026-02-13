<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use App\Models\AgentSystem;
use App\Models\SystemPrompt;
use App\Contracts\RagServiceInterface;
use App\Services\VectorSearchService;
use App\Services\CanopyRagService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ChatStreamController extends Controller
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
        $userId = $request->user() ? $request->user()->id : null;

        // Logic: Ưu tiên tìm theo ID trước, nếu không có thì tìm theo Type
        // Default values - Lấy từ bảng system_prompts
        $prompt = SystemPrompt::getPromptOrDefault('default_assistant', 'Bạn là trợ lý ảo.');
        $vectorStoreId = "";
        $aiModel = "gpt-4o";

        if ($agentType === 'canopy') {
            // Handle Custom Brand Agent
            if ($agentId) {
                $brandAgent = \App\Models\BrandAgent::find($agentId);
                if ($brandAgent) {
                    $prompt = $brandAgent->instruction ?? $prompt;
                    $vectorStoreId = $brandAgent->vector_id ?? "";

                    if ($brandAgent->is_include && $brandId) {
                        $brand = \App\Models\Brand::find($brandId);
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
            // Handle System Agent (Original Logic)
            $agentConfig = null;

            if ($agentId) {
                $agentConfig = AgentSystem::find($agentId);
            }

            if (!$agentConfig && $agentType) {
                $agentConfig = AgentSystem::where('agent_type', $agentType)->first();
            }

            if ($agentConfig) {
                $prompt = $agentConfig->prompt ?? $prompt;
                $vectorStoreId = $agentConfig->vector_id ?? $vectorStoreId;
                $aiModel = $agentConfig->model ?? $aiModel;
            }
        }

        // Logic accumulated context from previous steps
        $sequence = ['root1', 'root2', 'root3', 'trunk1', 'trunk2'];
        $currentIndex = array_search($agentType, $sequence);

        if ($brandId && $currentIndex !== false && $currentIndex > 0) {
            $brand = \App\Models\Brand::find($brandId);
            if ($brand) {
                $rootData = $brand->root_data ?? [];
                $trunkData = $brand->trunk_data ?? [];
                $contextParts = [];

                // Iterate through all previous steps
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

        // 1. Create Conversation if new
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

        $conversationId = $chat->conversation_id;

        // === Đính kèm nội dung file vào tin nhắn ===
        // Đợi các file đang processing hoàn tất
        $this->ragService->waitForPendingFiles('App\\Models\\Chat', $chat->id);

        // Lấy toàn bộ text đã trích xuất từ file đính kèm
        $attachedFiles = \App\Models\UploadedFile::forChat($chat->id)
            ->completed()
            ->get();

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
        // Nếu đang chat với canopy agent, kiểm tra agent có file đính kèm
        // Nếu có → reformulate question + search chunks → nối vào $userInput
        // (SONG SONG với chat-level file ở trên, không thay thế)
        if ($agentType === 'canopy' && $agentId) {
            $userInput = $this->canopyRagService->buildRagEnhancedInput(
                $userInput,
                (int) $agentId,
                $chat->id
            );
        }
        // === End RAG Agent-Level ===

        // Save User Message (bao gồm cả nội dung file đính kèm)
        Message::create([
            'chat_id' => $chat->id,
            'role' => 'user',
            'content' => $userInput
        ]);

        // 2. Stream Response
        // 2. Stream Response
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

                $log = $loggingService->log(
                    $chat->id ?? null,
                    $userId ?? null,
                    $brandId,
                    $bName,
                    $agentId,
                    $aName,
                    $agentType,
                    $aiModel ?? 'gpt-4o',
                    $userInput,
                    $prompt ?? ''
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Chat Logging Failed: " . $e->getMessage());
        }

        return response()->stream(function () use (&$conversationId, $userInput, $chat, $prompt, $vectorStoreId, $aiModel, $log, $loggingService) {
            $apiKey = env('OPENAI_API_KEY');

            // === Đảm bảo có OpenAI conversation ID hợp lệ ===
            // Nếu conversation_id chưa phải OpenAI format (ví dụ 'upload_xxx' từ file upload)
            // → tạo conversation mới trên OpenAI rồi lưu vào DB
            if (!$conversationId || !str_starts_with($conversationId, 'conv_')) {
                try {
                    $convResponse = Http::withToken($apiKey)
                        ->withOptions(['verify' => false])
                        ->post('https://api.openai.com/v1/conversations', [
                            'metadata' => [
                                'chat_id' => (string) $chat->id,
                                'topic' => mb_substr($userInput, 0, 50),
                            ]
                        ]);

                    $openAiConvId = $convResponse->json()['id'] ?? null;

                    if ($openAiConvId) {
                        $conversationId = $openAiConvId;
                        // Cập nhật conversation_id vào DB
                        $chat->update(['conversation_id' => $openAiConvId]);
                        Log::info("Created OpenAI conversation {$openAiConvId} for chat {$chat->id}");
                    } else {
                        Log::error('OpenAI Conversation creation failed', $convResponse->json() ?? []);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to create OpenAI conversation: ' . $e->getMessage());
                }
            }

            $url = 'https://api.openai.com/v1/responses';

            // Theo tài liệu: chỉ gửi tin nhắn mới nhất + conversation id
            // OpenAI tự quản lý toàn bộ lịch sử trong conversation
            $data = [
                'model' => $aiModel,
                'instructions' => $prompt,
                'input' => $userInput,
                'stream' => true,
            ];

            // Gửi conversation id nếu đã tạo thành công
            if ($conversationId && str_starts_with($conversationId, 'conv_')) {
                $data['conversation'] = [
                    'id' => $conversationId,
                ];
            }

            if ($vectorStoreId) {
                $data['tools'] = [
                    [
                        'type' => 'file_search',
                        'vector_store_ids' => [$vectorStoreId],
                        'max_num_results' => 20
                    ]
                ];
            }

            // Send initial JSON with conversation details
            echo json_encode(['db_chat_id' => $chat->id, 'event' => 'metadata', 'conv_id' => $chat->id]) . "\n\n";
            if (ob_get_level() > 0)
                ob_flush();
            flush();

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $buffer = '';
            $fullResponse = '';
            $inputTokens = 0;
            $outputTokens = 0;

            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) use (&$buffer, &$fullResponse, &$inputTokens, &$outputTokens) {
                echo $chunk;
                if (ob_get_level() > 0)
                    ob_flush();
                flush();

                $buffer .= $chunk;
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = substr($buffer, 0, $pos);
                    $buffer = substr($buffer, $pos + 1);
                    $line = trim($line);
                    if (strpos($line, 'data: ') === 0) {
                        $json = substr($line, 6);
                        if ($json === '[DONE]')
                            continue;

                        $data = json_decode($json, true);

                        // 1. Responses API (v1/responses)
                        // Check for 'response.completed' event which contains full text and usage
                        if (isset($data['type']) && $data['type'] === 'response.completed' && isset($data['response'])) {
                            $resp = $data['response'];

                            // Get Full Text from output array (Handle multiple types: e.g. text, tool_use)
                            if (isset($resp['output']) && is_array($resp['output'])) {
                                $fullResponse = ''; // Reset accumulation for final response
                                foreach ($resp['output'] as $outputItem) {
                                    if (isset($outputItem['content']) && is_array($outputItem['content'])) {
                                        foreach ($outputItem['content'] as $contentItem) {
                                            if (isset($contentItem['type']) && $contentItem['type'] === 'output_text' && isset($contentItem['text'])) {
                                                $fullResponse .= $contentItem['text'];
                                            }
                                        }
                                    }
                                }
                            }

                            // Get Token Usage
                            if (isset($resp['usage'])) {
                                $inputTokens = $resp['usage']['input_tokens'] ?? 0;
                                $outputTokens = $resp['usage']['output_tokens'] ?? 0;
                            }
                        }

                        // 2. Chat Completions API (Legacy/Fallback)
                        if (isset($data['choices'][0]['delta']['content'])) {
                            $fullResponse .= $data['choices'][0]['delta']['content'];
                        }

                        // Check usage for Chat Completions (if stream_options was relevant)
                        if (isset($data['usage']) && !isset($data['type'])) {
                            $inputTokens = $data['usage']['prompt_tokens'] ?? $inputTokens;
                            $outputTokens = $data['usage']['completion_tokens'] ?? $outputTokens;
                        }
                    }
                }

                return strlen($chunk);
            });

            curl_exec($ch);
            curl_close($ch);

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

    // Endpoint to save assistant message after streaming completes
    public function saveMessage(Request $request)
    {
        $chatId = $request->input('chat_id');
        $content = $request->input('content');

        if ($chatId && $content) {
            Message::create([
                'chat_id' => $chatId,
                'role' => 'assistant',
                'content' => $content
            ]);
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error'], 400);
    }

    private function createConversation($agentType, $agentId, $firstMessage, $brandId, $userId)
    {
        $apiKey = env('OPENAI_API_KEY');

        $response = Http::withToken($apiKey)->withOptions(['verify' => false])->post('https://api.openai.com/v1/conversations', [
            'metadata' => [
                'agentType' => $agentType,
                'agentId' => (string) $agentId,
                'topic' => substr($firstMessage, 0, 50)
            ]
        ]);

        $openAiConvId = $response->json()['id'] ?? null;

        if (!$openAiConvId) {
            Log::error('OpenAI Conversation creation failed', $response->json());
        }

        return Chat::create([
            'user_id' => $userId,
            'brand_id' => $brandId,
            'agent_id' => (int) $agentId,
            'agent_type' => $agentType,
            'model' => 'ChatGPT',
            'title' => 'Phiên làm việc ' . date('Y/m/d H:i:s'),
            'conversation_id' => $openAiConvId
        ]);
    }

    public function history(Request $request)
    {
        $brandId = $request->input('brandId');
        $agentId = $request->input('agentId');
        $agentType = $request->input('agentType');
        $userId = $request->user()->id;

        if (!$brandId || !$agentId) {
            return response()->json(['data' => []]);
        }

        $query = Chat::where('brand_id', $brandId)
            ->where('agent_id', $agentId)
            ->where('user_id', $userId);

        if ($agentType) {
            $query->where('agent_type', $agentType);
        }

        $chats = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($chats);
    }

    /**
     * Đổi tên đoạn chat
     * PUT /api/chat/{id}/rename
     */
    public function rename(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        // Tìm chat theo id
        $chat = Chat::findOrFail($id);

        // Kiểm tra quyền: chỉ chủ sở hữu mới được đổi tên
        if ($chat->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền chỉnh sửa'], 403);
        }

        // Cập nhật title
        $chat->title = $request->title;
        $chat->save();

        return response()->json([
            'success' => true,
            'title' => $chat->title
        ]);
    }

    /**
     * Format RAG chunks thành text để inject vào prompt
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

